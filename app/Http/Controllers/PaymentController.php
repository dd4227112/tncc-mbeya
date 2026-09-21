<?php

namespace App\Http\Controllers;

use App\Jobs\NotifiyUser;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Payment;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (hasPermission('payments.view')) {
            return view('pages.payments.index');
        } else {
            return redirect('dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }
    public function getpayments(Request $request)
    {
        if (hasPermission('payments.view')) {
            $dateRange = $request->input('date_range');
            $status = $request->input('status');
            $method = $request->input('method');

            if ($dateRange) {
                $dateRange = explode(' to ', $dateRange);

                $fromDate = $dateRange[0];
                $toDate = $dateRange[1];
            } else {
                $fromDate = date('Y-m-01');
                $toDate = date('Y-m-d');
            }

            try {
                $payments = Payment::with('invoice', 'receiver', 'payer')
                    ->whereDate('created_at', '>=', $fromDate)
                    ->whereDate('created_at', '<=', $toDate);
                if ($status) {
                    $payments->where('status', $status);
                }
                if ($method) {
                    $payments->where('payment_method', $method);
                }

                $payments = $payments->latest()->get();



                $data = $payments->map(function ($payment, $index) {
                    $status = ucfirst($payment->status ?? 'pending');
                    $statusClass = 'badge badge-soft-secondary';
                    $showPrint = ($payment->invoice && hasPermission('payments.print')) ? ('<button class="btn btn-sm btn-primary print-invoice" href="#" data-id="' . $payment->invoice->id . '">Print</button>') : '';
                    $showUssd = ''; // '<button class="btn btn-sm btn-success push-ussd" href="#" data-id="' . $payment->invoice->id . '">Push</button>';
                    $showDelete = hasPermission('payments.delete') ? '<button class="btn btn-sm btn-danger delete-payment" href="#" data-id="' . $payment->id . '">Delete</button>' : '';

                    if (strtolower($payment->status) === 'completed') {
                        $showDelete = '';
                        $showUssd = '';
                        $statusClass = 'badge badge-soft-success';
                    } elseif (strtolower($payment->status) === 'pending') {
                        // $showDelete = '';
                        $showPrint = '';
                        $statusClass = 'badge badge-soft-warning';
                    } elseif (strtolower($payment->status) === 'failed') {
                        $showPrint = '';
                        $showUssd = '';
                        $statusClass = 'badge badge-soft-danger';
                    }
                    return [
                        'id' => $index + 1,
                        'date' => optional($payment->date)->format('d M, Y'),
                        'payer' => optional($payment->payer)->name ?? 'N/A',
                        'amount' => number_format($payment->amount, 2),
                        'reference' => $payment->transaction_reference,
                        'invoice' => optional($payment->invoice)->reference_number,
                        'method' => $payment->payment_method,
                        'status' => $status,
                        'status_badge' => '<div class="' . $statusClass . ' font-size-12">' . $status . '</div>',
                        'processed' => optional($payment->receiver)->name ?? 'N/A',
                        'actions' => $showPrint . $showUssd . $showDelete
                    ];
                })->values();

                return response()->json(['data' => $data]);
            } catch (Throwable $e) {
                Log::error('Error fetching payments: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json([
                    'message' => 'Failed to get payments. Please try again later or contact support.',
                    'data' => [],
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'You do not have permission to view paymets.',
                'data' => [],
            ], 403);
        }
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
        if (hasPermission('payments.create')) {
            $request->validate([
                'invoice_id' => ['required', 'exists:invoices,id'],
                'method' => ['nullable', 'in:cash,mobile'],
                'phone' => [
                    'required',
                    'string',
                    'size:13',
                    function (string $attribute, mixed $value, Closure $fail) {
                        if (! isValidPhone($value)) {
                            $fail('The :attribute must be a valid Tanzanian phone number.');
                        }
                    },
                ],
                'network' => ['nullable', 'string', 'max:50'],
            ]);

            try {
                $invoice = Invoice::findOrFail($request->input('invoice_id'));
                $transaction_reference = 'CASH-' . time() . '-' . rand(1000, 9999);
                if ($request->input('method') === 'cash') {
                    DB::transaction(function () use ($invoice, $transaction_reference) {
                        $invoice->status = 'paid';
                        $invoice->save();

                        $invoice->payment()->create([
                            'received_by' => Auth::id(),
                            'user_id' => $invoice->user_id,
                            'amount' => $invoice->total_amount,
                            'status' => 'completed',
                            'payment_method' => 'cash',
                            'transaction_reference' => $transaction_reference,
                            'date' => now()->toDateString(),
                        ]);
                    });
                    // 'body' => "Habari, {$invoice->customer->name}! Malipo yako ya TSH." . number_format($invoice->total_amount, 2) . " yenye kumbumbuku namba: {$transaction_reference} kwa ajili ya ankra(invoice) namba: {$invoice->reference_number} yamepokelewa kikamilifu. Asante na Karibu tena!."
                    $message = Message::create([
                        'phone' => $request->input('phone'),
                        'body' => "Habari, {$invoice->customer->name}! Malipo yako ya TSH." . number_format($invoice->total_amount, 2) . " yenye kumbumbuku namba: {$transaction_reference} yamepokelewa kikamilifu. Asante na Karibu tena!.",
                        'status' => 'pending',
                        'reference' => $invoice->reference_number,
                    ]);
                    NotifiyUser::dispatch($message->id)->onQueue('sms-notifications');

                    return response()->json(
                        [
                            'message' => 'Payment added successfully.',
                            'data' => ['id' => $invoice->id]
                        ]
                    );
                } else {
                    $invoice->setAttribute('payment_method', 'mobile');
                    $invoice->setAttribute('phone', $request->input('phone'));
                    $invoice->setAttribute('network', $request->input('network'));
                    $customer = $invoice->customer;

                    return $this->createPaymentIntent($invoice, $customer);
                }
            } catch (Throwable $e) {
                Log::error('Error adding payment: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Failed to add payment. Please try again later.'], 500);
            }
        } else {
            return response()->json([
                'message' => 'You do not have permission to create paymets.',
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
    public function destroy(Payment $payment)
    {
        if (hasPermission('payments.delete')) {
            try {
                $payment->invoice()->update(['status' => 'pending']);
                $payment->delete();
            } catch (Throwable $e) {
                Log::error('Error deleting payment: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json([
                    'message' => 'Unable to delete payment. Please try again.',
                ], 500);
            }

            return response()->json([
                'message' => 'Payment deleted successfully.',
            ]);
        } else {
            return response()->json([
                'message' => 'You do not have permission to delete paymets.',
                'data' => [],
            ], 403);
        }
    }
    public function createPaymentIntent(Invoice $invoice, User $customer)
    {
        $message = 'Failed to initiate mobile payment.';
        try {
            $endpoint  =  '/v1/payments';
            $action = 'create';
            return $this->callAPI($endpoint, $invoice, $customer, $message, $action);
        } catch (Throwable $e) {
            Log::error($message . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => $message,
            ], 500);
        }
    }
    public function repushPayment(int $id)
    {
        $message = 'Failed to send USSD Push.';
        try {
            $invoice = Invoice::find($id);
            if ($invoice) {
                $endpoint = '/v1/payments/' . $invoice->reference_number . '/push';
                $action = 'update';
                $customer = $invoice->customer;
                return $this->callAPI($endpoint, $invoice, $customer, $message, $action);
            } else {
                return response()->json([
                    'message' => 'Invoice details not found.',
                ], 404);
            }
        } catch (Throwable $e) {
            Log::error($message . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => $message,
            ], 500);
        }
    }
    public function callAPI(string $endpoint, Invoice $invoice, User $customer, string $message, string $action)
    {
        $baseUrl = rtrim((string) config('snippe.base_url', ''), '/');
        $apiKey = trim((string) config('snippe.auth_token', ''));
        $webhookUrl = trim((string) config('snippe.snippe_webhook', ''));

        if ($baseUrl === '' || $apiKey === '' || $webhookUrl == '') {
            return response()->json(['message' => 'Payment gateway is not configured.'], 500);
        }

        $payload = [
            'payment_type' => 'mobile',
            'details' => [
                'amount' => (float) $invoice->total_amount,
                'currency' => 'TZS',
            ],
            'phone_number' => (string) $customer,
            'customer' => [
                'firstname' => (string) (optional($customer)->first_name ?? ''),
                'lastname' => (string) (optional($customer)->last_name ?? ''),
                'email' => (string) (optional($customer)->email ?? ''),
            ],
            'webhook_url' => $webhookUrl,
            'metadata' => [
                'order_id' => $invoice->reference_number,
                'invoice_id' => (string) $invoice->id,
                'network' => (string) ($invoice->getAttribute('network') ?? ''),
            ],
        ];

        $ch = curl_init($baseUrl . $endpoint);
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'Idempotency-Key: ' . $invoice->reference_number,
        ];


        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        if ($response === false || $curlError !== '') {
            Log::error($message . $curlError);

            return response()->json(['message' => $message], 500);
        }

        $decodedResponse = json_decode($response, true);
        $decodedResponse = is_array($decodedResponse) ? $decodedResponse : [];

        if (($httpCode < 200 || $httpCode >= 300) && empty($decodedResponse)) {
            return response()->json(['message' => $message], $httpCode ?: 500);
        }

        if (($httpCode >= 200 && $httpCode < 300) || ($decodedResponse['status'] ?? '') === 'success') {
            if ($action === 'create') {
                $invoice->payment()->create([
                    'received_by' => Auth::id(),
                    'user_id' => $invoice->user_id,
                    'amount' => $invoice->total_amount,
                    'status' => 'pending',
                    'payment_method' => 'mobile',
                    'transaction_reference' => $decodedResponse['data']['reference'] ?? null,
                    'date' => now()->toDateString(),
                ]);
            } else {
                $invoice->payment()->update(['status' => 'pending']);
            }
        }

        return response()->json($decodedResponse, $decodedResponse['code'] ?? ($httpCode >= 200 && $httpCode < 300 ? 201 : $httpCode));
    }
}
