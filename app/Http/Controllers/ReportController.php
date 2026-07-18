<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function collection(Request $request)
    {



        return view('pages.reports.collection');
        // Validate the request parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Fetch the report data based on the provided date range
        $reportData = $this->getReportData($request->start_date, $request->end_date);

        // Return the report data as a JSON response
        return response()->json($reportData);
    }
    public function getCollection(Request $request)
    {

        $build = self::reportQeuryBuilder();
        $date  = date('Y-m-d');
        if ($request->input('date_range')) {
            $dateRange = $request->input('date_range');
            $dateRange = explode(' to ', $dateRange);
            $fromDate = trim($dateRange[0] ?? '');
            $toDate = trim($dateRange[1] ?? '');
            if ($fromDate && $toDate) {
                $build->whereDate('t.created_at', '>=', $fromDate)
                    ->whereDate('t.created_at', '<=', $toDate);
                $title = $fromDate . ' to ' . $toDate;
            } else {
                $build->whereDate('t.created_at', '=', $fromDate);
                $title = $fromDate;
            }
        } else {
            $build->whereDate('t.created_at', $date);
            $title = $date;
        }
        $title = ': ' . $title;
        $reports =  $build->select(
            'p.date as date',
            'p.transaction_reference as reference',
            DB::raw("m.first_name || ' ' || m.last_name AS member_name"),
            'c.name as crop_name',
            'c.description as description',
            't.quantity as quantity',
            't.unit_price as rate',
            't.total_price as amount',
            DB::raw("b.first_name || ' ' || b.last_name AS payment_received_by")
        )
            ->where('p.status', 'completed')->get();
        $data =  $reports->map(function ($report, $key) {


            return [
                'id' => ($key + 1),
                'date' => $report->date = date('d-m-Y', strtotime($report->date)),
                'reference' => $report->reference,
                'member' => $report->member_name,
                'crop' => $report->crop_name . ' (' . $report->description . ')',
                'quantity' => $report->quantity,
                'rate' => $report->rate,
                'amount' => $report->amount,
                'user' => $report->payment_received_by,
            ];
        });
        return response()->json([
            'message' => 'Report fetched successfully',
            'data' => $data,
            'title' => $title
        ], 200);
    }
    public function payments(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Fetch the report data based on the provided date range
        $reportData = $this->getPaymentsReportData($request->start_date, $request->end_date);

        // Return the report data as a JSON response
        return response()->json($reportData);
    }
    public function crops(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        // Return the report data as a JSON response
        return response()->json($reportData);
    }
    public function getReportData($startDate, $endDate)
    {
        $reportData = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => [
                // Sample data
                ['date' => '2023-01-01', 'amount' => 100],
                ['date' => '2023-01-02', 'amount' => 200],
                ['date' => '2023-01-03', 'amount' => 150],
            ],
        ];
        return $reportData;
    }
    public function getPaymentsReportData($startDate, $endDate)
    {
        $reportData = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => [
                // Sample data
                ['date' => '2023-01-01', 'amount' => 50],
                ['date' => '2023-01-02', 'amount' => 75],
                ['date' => '2023-01-03', 'amount' => 100],
            ],
        ];
        return $reportData;
    }
    public function getCropsReportData($startDate, $endDate)
    {
        $reportData = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => [
                // Sample data
                ['crop' => 'Wheat', 'yield' => 1000],
                ['crop' => 'Corn', 'yield' => 1500],
                ['crop' => 'Rice', 'yield' => 1200],
            ],
        ];
        return $reportData;
    }

    private static function reportQeuryBuilder()
    {
        $query  = Invoice::join('invoice_items as t', 'invoices.id', '=', 't.invoice_id')
            ->join('payments as p', 'invoices.id', '=', 'p.invoice_id')
            ->join('crops as c', 't.crop_id', '=', 'c.id')
            ->join('users as m', 'invoices.user_id', '=', 'm.id') // member/ invoice for
            ->join('users as a', 'invoices.created_by', '=', 'a.id') // user who created invoice
            ->join('users as b', 'p.received_by', '=', 'b.id'); // user who created/processed payment
        return $query;
    }
}
