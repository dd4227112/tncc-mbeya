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

            if ($request->method === 'cash') {
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
                $invoice->payment_method = 'mobile';
                $invoice->phone = $request->input('phone');
                $invoice->network = $request->input('network');
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
    public function createPaymentIntent(Invoice $invoice, User $customer) {}
}
