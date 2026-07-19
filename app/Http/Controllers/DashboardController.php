<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $this->data['crops_counts']  = Crop::count();
        $this->data['members_counts'] = User::whereHas(
            'roles',
            fn($query) =>
            $query->where('roles.id', 4)
        )->count();
        $this->data['invoice_paid_amount']  = Invoice::whereIn('status', ['paid', 'pending'])->whereYear('created_at', date('Y'))->sum('total_amount');
        $this->data['paid_amounts']  = Payment::where('status', 'completed')->whereYear('created_at', date('Y'))->sum('amount');
        $invoice_summary = Invoice::select(
            'status',
            DB::raw('SUM(total_amount) as total_amount')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('status')
            ->get();

        $invoice_paid_summary = [];
        $total = 0;
        foreach ($invoice_summary as $key => $summary) {
            $invoice_paid_summary[$summary->status] = $summary->total_amount;
            $total += $summary->total_amount;
        }
        $paid = isset($invoice_paid_summary['paid']) ? $invoice_paid_summary['paid'] : 0;
        $this->data['invoice_paid_summary'] = $invoice_paid_summary;
        $this->data['invoice_grand_total'] = $total;
        $invoice_payment_percentage  = ($total > 0) ? ($paid / $total) : 0;
        $this->data['invoice_payment_percentage'] = floor($invoice_payment_percentage * 100);
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = DateTime::createFromFormat('!m', $i)->format('M');
        }
        // invoice detail for chars
        $invoicesData = Invoice::where('status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('extract(month from created_at) as month'),
                DB::raw('sum(total_amount) as amount')
            )
            ->groupBy(DB::raw('extract(month from created_at)'))
            ->get()
            ->keyBy('month')
            ->toArray();
        $invoicesDataArray = [];
        foreach ($months as $key => $month) {
            $invoicesDataArray[] = (isset($invoicesData[$key]) ? $invoicesData[$key]['amount'] : 0);
        }
        $invoicesDataArray = implode(',', $invoicesDataArray);
        $this->data['invoicesDataArray'] = $invoicesDataArray;

        // payment detail for chars
        $paymentsData = Payment::where('status', 'completed')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('extract(month from created_at) as month'),
                DB::raw('sum(amount) as amount')
            )
            ->groupBy(DB::raw('extract(month from created_at)'))
            ->get()
            ->keyBy('month')
            ->toArray();
        $paymentsDataArray = [];
        foreach ($months as $key => $month) {
            $paymentsDataArray[] = (isset($paymentsData[$key]) ? $paymentsData[$key]['amount'] : 0);
        }
        $paymentsDataArray = implode(',', $paymentsDataArray);
        $this->data['paymentsDataArray'] = $paymentsDataArray;

        // crops summary
        $cropsSummary = InvoiceItem::with('crop')
            ->select(
                'crop_id',
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(['crop_id'])
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        $total = $cropsSummary->sum('total');
        $cropsSummaryData = [];
        foreach ($cropsSummary as $key => $summary) {
            $cropsSummaryData[$summary->crop_id] = [
                'name' => $summary->crop->name,
                'count' => $summary->total,
                'percentage' => floor((($total > 0) ? ($summary->total / $total) : 0) * 100)
            ];
        }
        $this->data['cropsSummaryData'] = $cropsSummaryData;



        // payment status for chars
        $paymentStatus = Payment::whereYear('created_at', date('Y'))
            ->select(
                'status',
                DB::raw('sum(amount) as amount')
            )
            ->groupBy('status')
            ->get();


        $paymentStatusAmounts = [];
        $paymentStatusNames = [];
        $paymentStatusSummary = [];
        foreach ($paymentStatus as $key => $paymentStatus) {
            $paymentStatusNames[] = (string)ucfirst($paymentStatus->status);
            $paymentStatusAmounts[] = (int)$paymentStatus->amount;
            $paymentStatusSummary[$paymentStatus->status] = (int)$paymentStatus->amount;
        }
        $paymentStatusNames  = implode(',', array_map('json_encode', $paymentStatusNames));
        $paymentStatusAmounts = implode(',', $paymentStatusAmounts);
        $this->data['paymentStatusNames'] = $paymentStatusNames;
        $this->data['paymentStatusAmounts'] = $paymentStatusAmounts;
        $this->data['paymentStatusSummary'] = $paymentStatusSummary;



        // latest five invoices and payments - formatted for display
        $invoices = Invoice::with('customer', 'payment.receiver')->whereYear('created_at', date('Y'))->latest()->limit(5)->get();
        $formattedInvoices = $invoices->map(function ($invoice, $index) {
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
                'total_amount' => number_format($invoice->total_amount, 2),
                'status' => $status,
                'status_badge' => '<div class="' . $statusClass . ' font-size-12">' . $status . '</div>',
                'created_by' => optional($invoice->creator)->name ?? 'N/A',
            ];
        })->values();
        $this->data['invoices'] = $formattedInvoices;

        $payments = Payment::with('invoice', 'receiver', 'payer')->whereYear('created_at', date('Y'))->latest()->limit(5)->get();
        $formattedPayments = $payments->map(function ($payment, $index) {
            $status = ucfirst($payment->status ?? 'pending');
            $statusClass = 'badge badge-soft-secondary';

            if (strtolower($payment->status) === 'completed') {
                $statusClass = 'badge badge-soft-success';
            } elseif (strtolower($payment->status) === 'pending') {
                $statusClass = 'badge badge-soft-warning';
            } elseif (strtolower($payment->status) === 'failed') {
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
            ];
        })->values();
        $this->data['payments'] = $formattedPayments;

        return view('pages.dashboard', $this->data);
    }
}
