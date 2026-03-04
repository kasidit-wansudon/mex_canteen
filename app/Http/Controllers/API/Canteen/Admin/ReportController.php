<?php

namespace App\Http\Controllers\API\Canteen\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\Admin\ReportFilterRequest;
use App\Repositories\Canteen\ReportRepository;

class ReportController extends Controller
{
    /**
     * @var \App\Repositories\Canteen\ReportRepository
     */
    private $reportRepository;

    /**
     * @param  \App\Repositories\Canteen\ReportRepository  $reportRepository
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Return daily report rows or CSV.
     *
     * @param  \App\Http\Requests\Canteen\Admin\ReportFilterRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function daily(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $rows = $this->reportRepository->dailyReport($filters);

        if (($filters['export'] ?? null) === 'csv') {
            return $this->streamDailyCsv($rows);
        }

        return response()->json([
            'data' => $rows,
        ]);
    }

    /**
     * Return monthly aggregated report.
     *
     * @param  \App\Http\Requests\Canteen\Admin\ReportFilterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthly(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $rows = $this->reportRepository->monthlyReport($filters);

        return response()->json([
            'data' => $rows,
        ]);
    }

    /**
     * Return dynamic 7-day grid.
     *
     * @param  \App\Http\Requests\Canteen\Admin\ReportFilterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function weeklyGrid(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $rows = $this->reportRepository->sevenDayGrid($filters['week_start'] ?? null, $filters);

        return response()->json([
            'data' => $rows,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function streamDailyCsv($rows)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="canteen_daily_report.csv"',
        ];

        $columns = [
            'Date',
            'Staff type',
            'Staff code',
            'Staff name',
            'Email',
            'Meal type',
            'Status',
            'Meals',
            'Remark',
        ];

        return response()->stream(function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->reservation_date,
                    $row->staff_type,
                    $row->staff_code,
                    $row->staff_name,
                    $row->email ?: $row->visitor_email,
                    $row->meal_type,
                    $row->status_label,
                    $row->meal_count,
                    $row->remark_label,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
