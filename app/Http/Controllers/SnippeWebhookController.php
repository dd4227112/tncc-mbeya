<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SnippeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $rawPayload = $request->getContent();
        $timestamp = $request->header('X-Webhook-Timestamp');
        $signature = $request->header('X-Webhook-Signature');

        if (empty($rawPayload) || empty($timestamp) || empty($signature)) {
            return response('Missing webhook payload or headers', 400);
        }

        $eventTime = (int) $timestamp;
        if (time() - $eventTime > 300) {
            return response('Webhook timestamp too old', 400);
        }

        $signingKey = trim((string) config('snippe.webhook_secret', ''));
        if ($signingKey === '') {
            Log::warning('Snippe webhook secret is not configured.');

            return response('Webhook secret not configured', 500);
        }

        $message = $timestamp . '.' . $rawPayload;
        $expectedSignature = hash_hmac('sha256', $message, $signingKey);

        if (!hash_equals($signature, $expectedSignature)) {
            Log::warning('Invalid Snippe webhook signature received.', [
                'timestamp' => $timestamp,
                'signature' => $signature,
                'expected' => $expectedSignature,
            ]);

            return response('Invalid signature', 400);
        }

        try {
            $event = json_decode($rawPayload, true);
            if (!is_array($event)) {
                return response('Invalid JSON payload', 400);
            }

            $eventType = $event['type'] ?? $event['event'] ?? null;
            $eventData = is_array($event['data'] ?? null) ? $event['data'] : $event;
            $reference = $eventData['reference'] ?? $event['reference'] ?? null;
            $status = $eventData['status'] ?? $event['status'] ?? null;

            switch ($eventType) {
                case 'payment.completed':
                    $this->updatePayment($reference, 'completed', $eventData);
                    break;

                case 'payment.failed':
                    $this->updatePayment($reference, 'failed', $eventData);
                    break;

                case 'payment.voided':
                    $this->updatePayment($reference, 'voided', $eventData);
                    break;

                case 'payment.expired':
                    $this->updatePayment($reference, 'expired', $eventData);
                    break;

                case 'payout.completed':
                case 'payout.failed':
                case 'payout.reversed':
                    break;

                default:
                    Log::info('Unhandled Snippe webhook event', ['event_type' => $eventType]);
                    break;
            }

            return response('OK', 200);
        } catch (Throwable $e) {
            Log::error('Snippe webhook handling failed.', ['exception' => $e, 'payload' => $rawPayload]);

            return response('Webhook processing failed', 500);
        }
    }

    protected function updatePayment(?string $reference, string $status, array $eventData = []): void
    {
        if (empty($reference)) {
            return;
        }

        $payment = Payment::where('transaction_reference', $reference)->first();
        if (! $payment) {
            Log::warning('Snippe webhook received for unknown payment reference.', ['reference' => $reference]);

            return;
        }

        $payment->status = match ($status) {
            'completed' => 'completed',
            'failed' => 'failed',
            'voided' => 'failed',
            'expired' => 'failed',
            default => $payment->status,
        };
        $payment->save();

        $invoice = $payment->invoice()->first();
        if ($invoice) {
            $invoice->status = match ($status) {
                'completed' => 'paid',
                'failed' => 'pending',
                'voided' => 'cancelled',
                'expired' => 'cancelled',
                default => $invoice->status,
            };
            $invoice->save();
        }

        Log::info('Snippe webhook payment updated.', [
            'reference' => $reference,
            'status' => $status,
            'event_data' => $eventData,
        ]);
    }
}
