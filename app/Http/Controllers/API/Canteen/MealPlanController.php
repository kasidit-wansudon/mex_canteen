<?php

namespace App\Http\Controllers\API\Canteen;

use App\Http\Controllers\Controller;
use App\Http\Resources\Canteen\MealPlanResource;
use App\Services\Canteen\MealPlanService;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    /**
     * @var \App\Services\Canteen\MealPlanService
     */
    private $mealPlanService;

    /**
     * @param  \App\Services\Canteen\MealPlanService  $mealPlanService
     */
    public function __construct(MealPlanService $mealPlanService)
    {
        $this->mealPlanService = $mealPlanService;
    }

    /**
     * Return daily meal plans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function daily(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $plans = $this->mealPlanService->getDailyPlans($date);

        return response()->json([
            'data' => MealPlanResource::collection($plans),
        ]);
    }
}
