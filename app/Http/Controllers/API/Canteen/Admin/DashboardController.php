<?php

namespace App\Http\Controllers\API\Canteen\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Canteen\DashboardStatsResource;
use App\Repositories\Canteen\ReportRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
     * Return dashboard summary and chart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        $period = $request->query('period', 'day');
        $date = $request->query('date');

        $stats = $this->reportRepository->dashboardStats($period, $date);

        return response()->json([
            'data' => new DashboardStatsResource($stats),
        ]);
    }
}
