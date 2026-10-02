<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\Crop;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Role;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (hasPermission('invoices.view')) {
            $this->data['roles'] = Role::all();
            $this->data['role'] = 'members';
            $this->data['units'] = Unit::all();
            return view('pages.invoices.index', $this->data);
        } else {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view invoices.');
        }
    }

    public function getInvoices(Request $request)
    {
        if (hasPermission('invoices.view')) {
            try {
                $invoicesQuery = Invoice::with('customer', 'payment.receiver');
                if ($request->input('date_range')) {
                    $dateRange = $request->input('date_range');
                    $dateRange = explode(' to ', $dateRange);
                    $fromDate = trim($dateRange[0] ?? '');
                    $toDate = trim($dateRange[1] ?? '');
                    if ($fromDate && $toDate) {
                        $invoicesQuery->whereDate('created_at', '>=', $fromDate)
                            ->whereDate('created_at', '<=', $toDate);
                    } else {
                        $invoicesQuery->whereDate('created_at', '=', $fromDate);
                    }
                } else {
                    $fromDate = date('Y-m-01');
                    $toDate = date('Y-m-d');
                    $invoicesQuery->whereDate('created_at', '>=', $fromDate)
                        ->whereDate('created_at', '<=', $toDate);
                }
                $status = $request->input('status');
                if ($status) {
                    $invoicesQuery->where('status', $status);
                }

                $invoices = $invoicesQuery->latest()->get();

                $data = $invoices->map(function ($invoice, $index) {
                    $status = ucfirst($invoice->status ?? 'pending');
                    $statusClass = 'badge badge-soft-secondary';
                    $showAddPayment = hasPermission('payments.create') ? ('<button class="btn btn-sm btn-soft-info add-payment" data-bs-toggle="modal" type="button" data-id="' . $invoice->id . '"><i class="bx bx-plus-circle me-1"></i>Add Payment</button>') : '';
                    $showDelete = hasPermission('invoices.delete') ? ('<button class="btn btn-sm btn-soft-danger delete-invoice" type="button" data-id="' . $invoice->id . '"><i class="bx bx-trash me-1"></i>Delete</button>') : '';
                    $showPrint = hasPermission('invoices.print') ?  ('<button class="btn btn-sm btn-soft-primary print-invoice" type="button" data-id="' . $invoice->id . '"><i class="bx bx-printer me-1"></i>Print</button>') : '';
                    $showView = hasPermission('invoices.view') ? ('<button class="btn btn-sm btn-soft-secondary view-invoice" type="button" data-id="' . $invoice->id . '"><i class="bx bx-show me-1"></i>View</button>') : '';

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
                        'actions' => '<div class="d-flex flex-wrap gap-1">'
                            . $showView
                            . $showAddPayment
                            . $showPrint
                            . $showDelete .
                            '</div>',
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to view invoices.',
                'data' => [],
            ], 403);
        }
    }

    public function details(int $id)
    {
        if (hasPermission('invoices.view') || hasPermission('invoices.print')) {
            $invoice = Invoice::with(['customer', 'payment', 'creator', 'items.crop.unit'])->findOrFail($id);
            return response()->json(['data' => $this->invoiceDetailsData($invoice)]);
        } else {
            return response()->json([
                'message' => 'You do not have permission to view invoices.',
                'data' => [],
            ], 403);
        }
    }

    public function receipt(int $id)
    {
        abort_unless(
            hasPermission('invoices.view') ||
            hasPermission('invoices.print') ||
            hasPermission('payments.print'),
            403
        );

        $invoice = Invoice::with(['customer', 'payment', 'creator', 'items.crop.unit'])->findOrFail($id);
        $verificationUrl = URL::signedRoute('invoices.verify', [
            'reference' => $invoice->reference_number,
        ]);
        $qrCode = (new Writer(new ImageRenderer(new RendererStyle(300, 4), new SvgImageBackEnd())))
            ->writeString($verificationUrl);

        return view('invoices.receipt', [
            'invoice' => $this->invoiceDetailsData($invoice),
            'verificationUrl' => $verificationUrl,
            'qrCode' => 'data:image/svg+xml;base64,' . base64_encode($qrCode),
            'organizationAddress' => config('app.organization_address'),
            'organizationEmail' => config('app.organization_email'),
            'organizationPhone' => config('app.organization_phone'),
        ]);
    }

    public function verify(Request $request, string $reference)
    {
        abort_unless($request->hasValidSignature(), 404);

        $invoice = Invoice::with('payment')
            ->where('reference_number', $reference)
            ->firstOrFail();

        return view('invoices.verify', ['invoice' => $invoice]);
    }

    private function invoiceDetailsData(Invoice $invoice): array
    {
        $items = $invoice->items->map(function ($item, $index) {
            return [
                'index' => $index + 1,
                'name' => optional($item->crop)->name ?? 'Unknown crop',
                'unit' => optional(optional($item->crop)->unit)->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 2),
                'total_price' => number_format($item->total_price, 2),
            ];
        })->values();

        $payment = $invoice->payment;
        $customerAddress = optional($invoice->customer)->address;

        return [
            'id' => $invoice->id,
            'reference_number' => $invoice->reference_number,
            'date' => optional($invoice->date)->format('d M, Y'),
            'time' => optional($invoice->created_at)->format('H:i'),
            'member_name' => optional($invoice->customer)->name ?? 'N/A',
            'member_address' => $customerAddress ?: '—',
            'member_email' => optional($invoice->customer)->email ?? '—',
            'member_phone' => optional($invoice->customer)->phone ?? '—',
            'created_by' => optional($invoice->creator)->name ?? 'N/A',
            'sub_total' => number_format($invoice->items->sum('total_price'), 2),
            'total_amount' => number_format($invoice->total_amount, 2),
            'status' => strtoupper($invoice->status ?? 'pending'),
            'transaction_reference' => optional($payment)->transaction_reference,
            'payment_status' => strtoupper(optional($payment)->status ?? 'PENDING'),
            'payment_method' => strtoupper(optional($payment)->payment_method ?? ''),
            'paid_amount' => $payment ? number_format($payment->amount, 2) : null,
            'items' => $items,
            'item_count' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            // These optional fields are not stored by this application's invoice model.
            'vat' => null,
            'discount' => null,
            'change' => null,
            'terminal' => null,
            'barcode' => null,
        ];
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
        if (hasPermission('invoices.create')) {
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to create invoices.',
                'data' => [],
            ], 403);
        }
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
        if (hasPermission('invoices.delete')) {
            try {
                $invoice = Invoice::findOrFail($id);
                $invoice->items()->delete(); // Delete associated items first
                $invoice->delete(); // Then delete the invoice itself
            } catch (Throwable $e) {
                Log::error('Error deleting invoice: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Failed to delete invoice. Please try again later.'], 500);
            }

            return response()->json(['message' => 'Invoice deleted successfully.']);
        } else {
            return response()->json([
                'message' => 'You do not have permission to delete invoices.',
                'data' => [],
            ], 403);
        }
    }
}
