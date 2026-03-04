<?php

namespace App\Repositories\Canteen;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    /**
     * Build date range from period.
     *
     * @param  string  $period
     * @param  string|null  $date
     * @return array<string, string>
     */
    public function resolveRange($period, $date = null)
    {
        $anchor = $date ? Carbon::parse($date) : now();

        if ($period === 'week') {
            $start = $anchor->copy()->startOfWeek(Carbon::MONDAY);
            $end = $anchor->copy()->endOfWeek(Carbon::SUNDAY);
        } elseif ($period === 'month') {
            $start = $anchor->copy()->startOfMonth();
            $end = $anchor->copy()->endOfMonth();
        } else {
            $start = $anchor->copy()->startOfDay();
            $end = $anchor->copy()->endOfDay();
        }

        return [
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
        ];
    }

    /**
     * Aggregate dashboard stats for day/week/month.
     *
     * @param  string  $period
     * @param  string|null  $date
     * @return array<string, mixed>
     */
    public function dashboardStats($period, $date = null)
    {
        $range = $this->resolveRange($period, $date);

        $base = DB::table('reservations')
            ->whereBetween('reservation_date', [$range['date_from'], $range['date_to']]);

        $reservations = (clone $base)
            ->whereIn('status', ['confirmed', 'collected', 'expired'])
            ->sum('meal_count');

        $collected = (clone $base)
            ->where('status', 'collected')
            ->sum('meal_count');

        $noShows = (clone $base)
            ->whereIn('status', ['confirmed', 'expired'])
            ->sum('meal_count');

        $chart = (clone $base)
            ->select(
                'reservation_date',
                DB::raw("SUM(CASE WHEN status IN ('confirmed','collected','expired') THEN meal_count ELSE 0 END) AS reserved_count"),
                DB::raw("SUM(CASE WHEN status = 'collected' THEN meal_count ELSE 0 END) AS collected_count")
            )
            ->groupBy('reservation_date')
            ->orderBy('reservation_date')
            ->get();

        return [
            'period' => $period,
            'date_from' => $range['date_from'],
            'date_to' => $range['date_to'],
            'reservations' => (int) $reservations,
            'collected' => (int) $collected,
            'no_shows' => (int) $noShows,
            'chart' => $chart,
        ];
    }

    /**
     * Build the daily detail report table.
     *
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection
     */
    public function dailyReport(array $filters)
    {
        $query = DB::table('reservations AS r')
            ->leftJoin('users AS u', 'u.id', '=', 'r.user_id')
            ->leftJoin('visitors AS v', 'v.id', '=', 'r.visitor_id')
            ->leftJoin('meal_collections AS mc', 'mc.reservation_id', '=', 'r.id')
            ->select(
                'r.id',
                'r.reservation_date',
                'r.meal_type',
                'r.reservation_type',
                'r.status',
                'r.visitor_count',
                'r.meal_count',
                'r.pickup_for_staff_code',
                'r.remark',
                'r.qr_expiry_time',
                'u.staff_type',
                'u.staff_code',
                'u.staff_name',
                'u.email',
                'v.visitor_name',
                'v.email AS visitor_email',
                'mc.collector_staff_code',
                'mc.collector_name',
                'mc.collected_at'
            )
            ->orderByDesc('r.reservation_date')
            ->orderByDesc('r.id');

        if (!empty($filters['date_from'])) {
            $query->whereDate('r.reservation_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('r.reservation_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['staff_type'])) {
            $query->where('u.staff_type', $filters['staff_type']);
        }

        if (!empty($filters['staff_code'])) {
            $query->where('u.staff_code', 'like', '%' . $filters['staff_code'] . '%');
        }

        if (!empty($filters['staff_name'])) {
            $query->where('u.staff_name', 'like', '%' . $filters['staff_name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where(function ($sub) use ($filters) {
                $sub->where('u.email', 'like', '%' . $filters['email'] . '%')
                    ->orWhere('v.email', 'like', '%' . $filters['email'] . '%');
            });
        }

        if (!empty($filters['reservation_status'])) {
            if ($filters['reservation_status'] === 'reserved_collected') {
                $query->where('r.status', 'collected');
            } elseif ($filters['reservation_status'] === 'reserved_uncollected') {
                $query->whereIn('r.status', ['confirmed', 'expired']);
            } elseif ($filters['reservation_status'] === 'reserved_picked_up_by_proxy') {
                $query->whereNotNull('mc.collector_staff_code')
                    ->whereColumn('mc.collector_staff_code', '!=', 'u.staff_code');
            }
        }

        return $query->get()->map(function ($row) {
            $row->status_label = $this->mapStatusLabel($row);
            $row->remark_label = $this->mapRemark($row);
            return $row;
        });
    }

    /**
     * Build monthly aggregated statistics.
     *
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection
     */
    public function monthlyReport(array $filters)
    {
        $from = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfMonth()->toDateString() : now()->startOfMonth()->toDateString();
        $to = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfMonth()->toDateString() : now()->endOfMonth()->toDateString();

        $query = DB::table('users AS u')
            ->leftJoin('reservations AS r', function ($join) use ($from, $to) {
                $join->on('u.id', '=', 'r.user_id')
                    ->whereBetween('r.reservation_date', [$from, $to]);
            })
            ->leftJoin('meal_collections AS mc', 'mc.reservation_id', '=', 'r.id')
            ->select(
                'u.id',
                'u.staff_type',
                'u.staff_code',
                'u.staff_name',
                'u.email',
                DB::raw("SUM(CASE WHEN r.status IN ('confirmed','collected','expired') THEN r.meal_count ELSE 0 END) AS allocated_meals"),
                DB::raw("COUNT(CASE WHEN r.id IS NOT NULL THEN 1 END) AS reservation_count"),
                DB::raw("SUM(CASE WHEN r.status = 'collected' THEN r.meal_count ELSE 0 END) AS actual_collected"),
                DB::raw("SUM(CASE WHEN r.status IN ('confirmed','expired') THEN r.meal_count ELSE 0 END) AS no_show_count"),
                DB::raw("SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END) AS cancellation_count"),
                DB::raw("SUM(CASE WHEN r.reservation_type IN ('self_pickup','pickup_only') THEN r.meal_count ELSE 0 END) AS proxy_pickups_done"),
                DB::raw("SUM(CASE WHEN r.pickup_for_user_id IS NOT NULL THEN r.meal_count ELSE 0 END) AS picked_up_by_proxy"),
                DB::raw("SUM(CASE WHEN r.visitor_count > 0 THEN r.visitor_count ELSE 0 END) AS visitor_meal_count")
            )
            ->groupBy('u.id', 'u.staff_type', 'u.staff_code', 'u.staff_name', 'u.email')
            ->orderBy('u.staff_code');

        if (!empty($filters['staff_type'])) {
            $query->where('u.staff_type', $filters['staff_type']);
        }

        if (!empty($filters['staff_code'])) {
            $query->where('u.staff_code', 'like', '%' . $filters['staff_code'] . '%');
        }

        if (!empty($filters['staff_name'])) {
            $query->where('u.staff_name', 'like', '%' . $filters['staff_name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('u.email', 'like', '%' . $filters['email'] . '%');
        }

        return $query->get()->map(function ($row) {
            $row->cost_calculation = (int) $row->no_show_count + (int) $row->visitor_meal_count;
            return $row;
        });
    }

    /**
     * Build 7-day status grid for each staff.
     *
     * @param  string|null  $weekStart
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection
     */
    public function sevenDayGrid($weekStart = null, array $filters = [])
    {
        $start = $weekStart ? Carbon::parse($weekStart)->startOfDay() : now()->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->addDays(6);

        $usersQuery = DB::table('users')
            ->select('id', 'staff_code', 'staff_name', 'staff_type', 'email')
            ->orderBy('staff_code');

        if (!empty($filters['staff_type'])) {
            $usersQuery->where('staff_type', $filters['staff_type']);
        }

        $users = $usersQuery->get();

        $reservations = DB::table('reservations AS r')
            ->leftJoin('meal_collections AS mc', 'mc.reservation_id', '=', 'r.id')
            ->select(
                'r.user_id',
                'r.reservation_date',
                'r.reservation_type',
                'r.visitor_count',
                'r.status',
                'r.pickup_for_staff_code',
                'mc.collector_staff_code'
            )
            ->whereBetween('r.reservation_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('r.user_id')
            ->get()
            ->groupBy(function ($row) {
                return $row->user_id . '|' . $row->reservation_date;
            });

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $start->copy()->addDays($i)->toDateString();
        }

        return $users->map(function ($user) use ($days, $reservations) {
            $grid = [];

            foreach ($days as $day) {
                $key = $user->id . '|' . $day;
                $items = $reservations->get($key, collect());
                $grid[$day] = $this->buildCellLabel($items);
            }

            return [
                'staff_code' => $user->staff_code,
                'staff_name' => $user->staff_name,
                'staff_type' => $user->staff_type,
                'email' => $user->email,
                'grid' => $grid,
            ];
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $items
     * @return string
     */
    private function buildCellLabel(Collection $items)
    {
        if ($items->isEmpty()) {
            return '未预约';
        }

        $row = $items->first();

        if ($row->status === 'collected') {
            if ((int) $row->visitor_count > 0) {
                return '已取餐：自己+访客 ' . $row->visitor_count;
            }

            if (!empty($row->pickup_for_staff_code)) {
                return '已取餐（代取' . $row->pickup_for_staff_code . '）';
            }

            return '已取餐';
        }

        if (in_array($row->status, ['confirmed', 'expired'], true)) {
            return '未取餐';
        }

        if ($row->status === 'cancelled') {
            return '已取消';
        }

        return '未取餐';
    }

    /**
     * @param  object  $row
     * @return string
     */
    private function mapStatusLabel($row)
    {
        if ($row->status === 'collected') {
            if ($row->reservation_type === 'self_invitation') {
                return '自己+访客';
            }

            if ($row->reservation_type === 'invitation_only') {
                return '仅访客';
            }

            if ($row->reservation_type === 'self_pickup') {
                return '自己+代取';
            }

            if ($row->reservation_type === 'pickup_only') {
                return '仅代取';
            }

            return '已就餐';
        }

        if ($row->status === 'cancelled') {
            return '已取消';
        }

        return '未就餐';
    }

    /**
     * @param  object  $row
     * @return string|null
     */
    private function mapRemark($row)
    {
        if (!empty($row->collector_staff_code) && $row->collector_staff_code !== $row->staff_code) {
            return 'picked up by ' . $row->collector_staff_code;
        }

        if ((int) $row->visitor_count > 0) {
            return 'visitor meals ' . $row->visitor_count;
        }

        return $row->remark;
    }
}
