<?php

namespace App\Services\Canteen;

use App\Models\MealPlan;
use Illuminate\Support\Facades\Cache;

class MealPlanService
{
    /**
     * Load daily meal plans with cache.
     *
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDailyPlans($date)
    {
        $cacheKey = sprintf('canteen:meal_plans:%s', $date);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date) {
            return MealPlan::query()
                ->whereDate('meal_date', $date)
                ->where('status', 'published')
                ->orderByRaw("FIELD(meal_type, 'lunch', 'dinner')")
                ->get();
        });
    }

    /**
     * Invalidate daily cache after meal plan updates.
     *
     * @param  string  $date
     * @return void
     */
    public function clearDailyPlanCache($date)
    {
        Cache::forget(sprintf('canteen:meal_plans:%s', $date));
    }
}
