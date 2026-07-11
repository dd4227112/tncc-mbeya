<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.invoices.index');
    }

    public function getInvoices()
    {
        try {
            $invoices = Invoice::with('customer', 'payment.receiver')->latest()->get();

            $data = $invoices->map(function ($invoice, $index) {
                $status = ucfirst($invoice->status ?? 'pending');
                $statusClass = 'badge badge-soft-secondary';
                $showAddPayment = '<li><a class="dropdown-item text-info add-payment" data-bs-toggle="modal" type="button" data-id="' . $invoice->id . '"><i class="bx bx-plus-circle me-2"></i>Add Payment</a></li>';
                $showDelete = '<li><a class="dropdown-item text-danger delete-invoice" type="button" data-id="' . $invoice->id . '"><i class="bx bx-trash me-2"></i>Delete</a></li>';

                if (strtolower($invoice->status) === 'paid') {
                    $showDelete = '';
                    $showAddPayment = '';
                    $statusClass = 'badge badge-soft-success';
                } elseif (strtolower($invoice->status) === 'pending') {
                    $statusClass = 'badge badge-soft-warning';
                } elseif (strtolower($invoice->status) === 'overdue') {
                    $showDelete = '';
                    $showAddPayment = '';
                    $statusClass = 'badge badge-soft-danger';
                }

                return [
                    'id' => $index + 1,
                    'reference_number' => $invoice->reference_number,
                    'date' => optional($invoice->date)->format('d M, Y'),
                    'member' => optional($invoice->customer)->name ?? 'N/A',
                    'total_amount' => 'TZS ' . number_format($invoice->total_amount, 2),
                    'status' => $status,
                    'status_badge' => '<div class="' . $statusClass . ' font-size-12">' . $status . '</div>',
                    'created_by' => optional($invoice->creator)->name ?? 'N/A',
                    // 'actions' => '
                    // <button class="btn btn-sm btn-soft-secondary view-invoice" type="button" data-id="' . $invoice->id . '">View</button>
                    // <button class="btn btn-sm btn-soft-info add-payment" data-bs-toggle="modal" type="button" data-id="' . $invoice->id . '">Add Payment</button>
                    // <button class="btn btn-sm btn-soft-primary print-invoice" type="button" data-id="' . $invoice->id . '">Print</button>
                    // <button class="btn btn-sm btn-soft-danger delete-invoice" type="button" data-id="' . $invoice->id . '">Delete</button>
                    // ',
                    'actions' =>
                    '<div class="dropdown"><button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-horizontal-rounded"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item text-secondary view-invoice" type="button" data-id="' . $invoice->id . '">
                                    <i class="bx bx-show me-2"></i>View
                                </a>
                            </li>'
                        . $showAddPayment .
                        '<li>
                                <a class="dropdown-item text-primary print-invoice" type="button" data-id="' . $invoice->id . '">
                                    <i class="bx bx-printer me-2"></i>Print
                                </a>
                            </li>'
                        . $showDelete .
                        '</ul>
                    </div>',
                ];
            })->values();

            return response()->json(['data' => $data]);
        } catch (Throwable $e) {
            Log::error('Error fetching invoices: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to get invoices. Please try again later or contact support.',
                'data' => [],
            ], 500);
        }
    }

    public function details(int $id)
    {
        $invoice = Invoice::with(['customer', 'payment', 'creator', 'items.crop.unit'])->findOrFail($id);

        $items = $invoice->items->map(function ($item, $index) {
            return [
                'index' => $index + 1,
                'name' => optional($item->crop)->name ?? 'Unknown crop',
                'unit' => optional($item->crop->unit)->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_price' => 'TZS ' . number_format($item->unit_price, 2),
                'total_price' => 'TZS ' . number_format($item->total_price, 2),
            ];
        })->values();

        return response()->json(['data' => [
            'id' => $invoice->id,
            'reference_number' => $invoice->reference_number,
            'date' => optional($invoice->date)->format('d M, Y'),
            'member_name' => optional($invoice->customer)->name ?? 'N/A',
            'member_address' => optional($invoice->customer)->address ?? '—',
            'member_email' => optional($invoice->customer)->email ?? '—',
            'member_phone' => optional($invoice->customer)->phone ?? '—',
            'created_by' => optional($invoice->creator)->name ?? 'N/A',
            'sub_total' => 'TZS ' . number_format($invoice->items->sum('total_price'), 2),
            'total_amount' => 'TZS ' . number_format($invoice->total_amount, 2),
            'status' => strtoupper($invoice->status ?? 'pending'),
            'transaction_reference' => optional($invoice->payment)->transaction_reference ?? null,
            'payment_status' => strtoupper(optional($invoice->payment)->status ?? 'PENDING'),
            'payment_method' => strtoupper(optional($invoice->payment)->payment_method ?? null),
            'items' => $items,
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'member_id' => ['required', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.crop_id' => ['required', 'exists:crops,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $items = $request->input('items');

            $invoice = DB::transaction(function () use ($request, $items) {
                $cropIds = collect($items)->pluck('crop_id');
                $crops = Crop::whereIn('id', $cropIds)->get()->keyBy('id');

                $invoiceItemData = [];
                $totalAmount = 0;

                foreach ($items as $item) {
                    $crop = $crops->get($item['crop_id']);

                    if (!$crop) {
                        // throwing inside the closure triggers an automatic rollback
                        abort(422, 'Invalid crop selected.');
                    }

                    $lineTotal = $item['quantity'] * $crop->price;
                    $totalAmount += $lineTotal;

                    $invoiceItemData[] = [
                        'crop_id' => $crop->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $crop->price,
                        'total_price' => $lineTotal,
                    ];
                }

                $invoice = Invoice::create([
                    'reference_number' => 'INV-' . date('YmdHis'),
                    'user_id' => $request->input('member_id'),
                    'total_amount' => $totalAmount, // computed server-side, not from the request
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                    'date' => now()->toDateString(),
                ]);

                foreach ($invoiceItemData as $itemData) {
                    InvoiceItem::create(array_merge($itemData, ['invoice_id' => $invoice->id]));
                }

                return $invoice;
            });
        } catch (\Exception $e) {
            Log::error('Error saving invoice: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Failed to save invoice. Please try again later.'], 500);
        }

        return response()->json([
            'message' => 'Invoice saved successfully.',
            'data' => ['id' => $invoice->id],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implement the logic to delete an invoice and its associated items
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->items()->delete(); // Delete associated items first
            $invoice->delete(); // Then delete the invoice itself
        } catch (Exception $e) {
            Log::error('Error deleting invoice: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Failed to delete invoice. Please try again later.'], 500);
        }

        return response()->json(['message' => 'Invoice deleted successfully.']);
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'method' => ['required', 'in:cash,mobile'],
            'phone' => ['required_if:method,mobile', 'nullable', 'string', 'max:20'],
            'network' => ['required_if:method,mobile', 'nullable', 'string', 'max:50'],
        ]);

        try {
            $invoice = Invoice::findOrFail($request->input('invoice_id'));

            if ($request->method === 'cash') {
                $invoice->status = 'paid';
                $invoice->save();
                $invoice->payments()->create([
                    'received_by' => Auth::id(),
                    'user_id' => $invoice->user_id,
                    'amount' => $invoice->total_amount,
                    'status' => 'completed',
                    'payment_method' => 'cash',
                    'transaction_reference' => 'CASH_' . date('YmdHis'),
                    'date' => now()->toDateString(),
                ]);
                return response()->json(
                    [
                        'message' => 'Payment added successfully.',
                        'data' => ['id' => $invoice->id]
                    ]
                );
            } else {
                $invoice->payment_method = 'mobile';
                $invoice->phone = $request->input('phone');
                $invoice->network = $request->input('network');
                $customer = $invoice->customer;
                return $this->createPaymentApi($invoice, $customer);
            }
        } catch (Exception $e) {
            Log::error('Error adding payment: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Failed to add payment. Please try again later.'], 500);
        }

        return response()->json(['message' => 'Payment added successfully.']);
    }
    public function createPaymentApi(Invoice $invoice, User $customer) {}
}
