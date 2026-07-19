<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportController extends Controller
{
    public function collection()
    {
        return view('pages.reports.collection');
    }
    public function getCollection(Request $request)
    {

        try {
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
        } catch (Throwable $e) {
            Log::error('Error fetching collection report: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to get collection report. Please try again later or contact support.',
                'data' => [],
            ], 500);
        }
    }
    public function crops()
    {
        return view('pages.reports.crop_performance');
    }
    public function getCropsReportData(Request $req)
    {
        try {
            $query = self::reportQeuryBuilder();
            $date  = date('Y-m-d');
            if ($req->input('date_range')) {
                $dateRange = $req->input('date_range');
                $dateRange = explode(' to ', $dateRange);
                $fromDate = trim($dateRange[0] ?? '');
                $toDate = trim($dateRange[1] ?? '');
                if ($fromDate && $toDate) {
                    $query->whereDate('t.created_at', '>=', $fromDate)
                        ->whereDate('t.created_at', '<=', $toDate);
                    $title = $fromDate . ' to ' . $toDate;
                } else {
                    $query->whereDate('t.created_at', '=', $fromDate);
                    $title = $fromDate;
                }
            } else {
                $query->whereDate('t.created_at', $date);
                $title = $date;
            }
            $title = ': ' . $title;
            $reportData = $query->select(
                'c.id',
                'c.name AS crop_name',
                DB::raw('COUNT(DISTINCT invoices.user_id) AS total_members'),
                DB::raw('SUM(t.quantity) AS total_weight'),
                DB::raw('ROUND(AVG(t.quantity), 2) AS average_weight_per_member'),
                DB::raw('SUM(t.total_price) AS total_collection')
            )->groupBy([
                'c.id',
                'c.name'
            ])
                ->get();
            $data  = $reportData->map(function ($report, $key) {
                return [
                    'id' => ++$key,
                    'crop_id' => $report->id,
                    'crop_name' => $report->crop_name,
                    'total_members' => $report->total_members,
                    'total_weight' => $report->total_weight,
                    'average_weight_per_member' => number_format($report->average_weight_per_member, 2),
                    'total_collection' => number_format($report->total_collection, 2),
                ];
            });
            return response()->json([
                'message' => 'Report fetched successfully',
                'data' => $data,
                'title' => $title
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error fetching crop performance: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to get  crop performance report. Please try again later or contact support.',
                'data' => [],
            ], 500);
        }
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
