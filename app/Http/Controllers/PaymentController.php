<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
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
        //
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
            'invoice_id' => ['required', 'exists:invoices,id'],
            'method' => ['required', 'in:cash,mobile'],
            'phone' => ['required_if:method,mobile', 'nullable', 'string', 'max:20'],
            'network' => ['required_if:method,mobile', 'nullable', 'string', 'max:50'],
        ]);

        try {
            $invoice = Invoice::findOrFail($request->input('invoice_id'));

            if ($request->input('method') === 'cash') {
                DB::transaction(function () use ($invoice) {
                    $invoice->status = 'paid';
                    $invoice->save();

                    $invoice->payment()->create([
                        'received_by' => Auth::id(),
                        'user_id' => $invoice->user_id,
                        'amount' => $invoice->total_amount,
                        'status' => 'completed',
                        'payment_method' => 'cash',
                        'transaction_reference' => 'CASH_' . date('YmdHis'),
                        'date' => now()->toDateString(),
                    ]);
                });

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

        return response()->json(['message' => 'Payment added successfully.']);
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
    public function createPaymentIntent(Invoice $invoice, User $customer)
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
            'phone_number' => (string) ($invoice->getAttribute('phone') ?? optional($customer)->phone ?? ''),
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

        $ch = curl_init($baseUrl . '/v1/payments');
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'Idempotency-Key: ' . ($invoice->reference_number ?: 'payment-' . $invoice->id . '-' . now()->timestamp),
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
            Log::error('Mobile payment request failed: ' . $curlError);

            return response()->json(['message' => 'Failed to initiate mobile payment.'], 500);
        }

        $decodedResponse = json_decode($response, true);
        $decodedResponse = is_array($decodedResponse) ? $decodedResponse : [];

        if (($httpCode < 200 || $httpCode >= 300) && empty($decodedResponse)) {
            return response()->json(['message' => 'Failed to initiate mobile payment.'], $httpCode ?: 500);
        }

        if (($httpCode >= 200 && $httpCode < 300) || ($decodedResponse['status'] ?? '') === 'success') {
            $invoice->payment()->create([
                'received_by' => Auth::id(),
                'user_id' => $invoice->user_id,
                'amount' => $invoice->total_amount,
                'status' => 'pending',
                'payment_method' => 'mobile',
                'transaction_reference' => $decodedResponse['data']['reference'] ?? null,
                'date' => now()->toDateString(),
            ]);
        }

        return response()->json($decodedResponse, $decodedResponse['code'] ?? ($httpCode >= 200 && $httpCode < 300 ? 201 : $httpCode));
    }
}
