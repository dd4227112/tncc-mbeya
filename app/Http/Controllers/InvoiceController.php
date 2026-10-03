<?php

namespace App\Http\Controllers;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
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

        // Sized for the 58mm thermal printer: modules are made as large as fit
        // in 46mm, and shrunk slightly to cancel thermal ink/heat spread.
        $qr = $this->buildQr($verificationUrl, 46.0, 0.5);

        return view('invoices.receipt', [
            'invoice' => $this->invoiceDetailsData($invoice),
            'verificationUrl' => $verificationUrl,
            'qrCode' => $qr['src'],
            'qrWidthMm' => $qr['width_mm'],
            'organizationAddress' => config('app.organization_address'),
            'organizationEmail' => config('app.organization_email'),
            'organizationPhone' => config('app.organization_phone'),
        ]);
    }

    public function verify(Request $request, string $reference)
    {
        abort_unless($request->hasValidSignature(), 404);

        $invoice = Invoice::with(['customer', 'payment'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        $verificationUrl = URL::signedRoute('invoices.verify', [
            'reference' => $invoice->reference_number,
        ]);

        // On-screen page: no printer limit and no dot gain to compensate.
        $qr = $this->buildQr($verificationUrl, null, 0.0, 6);

        return view('invoices.verify', [
            'invoice' => $invoice,
            'qrCode' => $qr['src'],
            'qrWidthMm' => $qr['width_mm'],
            'organizationEmail' => config('app.organization_email'),
            'organizationPhone' => config('app.organization_phone'),
        ]);
    }

    /**
     * @param string     $content        Text/URL to encode.
     * @param float|null $maxWidthMm     Fit within this width (null = use $dotsPerModule).
     * @param float      $dotGain        Dots to shave off each side of a module (0 - 1.5).
     * @param int        $dotsPerModule  Used only when $maxWidthMm is null.
     * @return array{src: string, width_mm: float, modules: int, dots_per_module: int}
     */
    private function buildQr(string $content, ?float $maxWidthMm = 46.0, float $dotGain = 0.5, int $dotsPerModule = 6): array
    {
        // BaconQrCode v3 uses an enum (L), v2 uses a static method (L()).
        $ecLevel = enum_exists(ErrorCorrectionLevel::class)
            ? ErrorCorrectionLevel::L
            : ErrorCorrectionLevel::L();

        $quietZone = 4; // modules of white around the code
        $dotsPerMm = 8; // 203dpi

        $matrix  = Encoder::encode($content, $ecLevel, 'UTF-8')->getMatrix();
        $modules = $matrix->getWidth();
        $totalModules = $modules + 2 * $quietZone;

        if ($maxWidthMm !== null) {
            $dotsPerModule = (int) floor($maxWidthMm * $dotsPerMm / $totalModules);
        }
        $dotsPerModule = max(3, min(10, $dotsPerModule));

        // Never shave more than a third of a module.
        $inset = min(max(0.0, $dotGain), $dotsPerModule / 3);
        $cell  = $dotsPerModule - 2 * $inset;
        $size  = $totalModules * $dotsPerModule;

        $path = '';
        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $px = ($x + $quietZone) * $dotsPerModule + $inset;
                    $py = ($y + $quietZone) * $dotsPerModule + $inset;
                    $path .= "M{$px} {$py}h{$cell}v{$cell}h-{$cell}z";
                }
            }
        }

        $svg = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" '
            . "width=\"{$size}\" height=\"{$size}\" viewBox=\"0 0 {$size} {$size}\" "
            . 'shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#ffffff"/>'
            . "<path fill=\"#000000\" d=\"{$path}\"/>"
            . '</svg>';

        $widthMm = round($size / $dotsPerMm, 2);

        Log::info('Receipt QR built', [
            'modules' => $modules,
            'dots_per_module' => $dotsPerModule,
            'dot_gain' => $inset,
            'width_mm' => $widthMm,
            'content_length' => strlen($content),
        ]);

        return [
            'src' => 'data:image/svg+xml;base64,' . base64_encode($svg),
            'width_mm' => $widthMm,
            'modules' => $modules,
            'dots_per_module' => $dotsPerModule,
        ];
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
            'location' => $invoice->location ?? '—',
            'plate_number' => $invoice->plate_number ?? '—',
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
                'location' => ['required', 'string', 'max:255'],
                'plate_number' => ['required', 'string', 'max:255'],
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
                        'location' => $request->input('location'),
                        'plate_number' => $request->input('plate_number'),
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