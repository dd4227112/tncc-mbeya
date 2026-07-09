<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            $invoices = Invoice::with('customer')->latest()->get();

            $data = $invoices->map(function ($invoice, $index) {
                $status = ucfirst($invoice->status ?? 'pending');
                $statusClass = 'badge badge-soft-secondary';

                if (strtolower($invoice->status) === 'paid') {
                    $statusClass = 'badge badge-soft-success';
                } elseif (strtolower($invoice->status) === 'pending') {
                    $statusClass = 'badge badge-soft-warning';
                } elseif (strtolower($invoice->status) === 'overdue') {
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
                    'actions' => '<button class="btn btn-sm btn-soft-info view-invoice" type="button" data-id="' . $invoice->id . '">View</button>
                    <button class="btn btn-sm btn-soft-primary print-invoice" type="button" data-id="' . $invoice->id . '">Print</button>
                    <button class="btn btn-sm btn-soft-danger delete-invoice" type="button" data-id="' . $invoice->id . '">Delete</button>
                    ',
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

    public function details($id)
    {
        $invoice = Invoice::with(['customer', 'creator', 'items.crop.unit'])->findOrFail($id);

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
            'status' => ucfirst($invoice->status ?? 'pending'),
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
            'invoice_id' => $invoice->id,
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
        //
    }
}
