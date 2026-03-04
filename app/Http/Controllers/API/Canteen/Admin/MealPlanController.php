<?php

namespace App\Http\Controllers\API\Canteen\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\Admin\StoreMealPlanRequest;
use App\Http\Resources\Canteen\MealPlanResource;
use App\Models\MealPlan;
use App\Services\Canteen\MealPlanService;

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
     * Create or update daily meal plan.
     *
     * @param  \App\Http\Requests\Canteen\Admin\StoreMealPlanRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMealPlanRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $mealPlan = MealPlan::query()->updateOrCreate(
            [
                'meal_date' => $validated['meal_date'],
                'meal_type' => $validated['meal_type'],
            ],
            [
                'menu_item_1' => $validated['menu_item_1'],
                'menu_item_2' => $validated['menu_item_2'] ?? null,
                'menu_item_3' => $validated['menu_item_3'] ?? null,
                'reservation_open_at' => $validated['reservation_open_at'] ?? null,
                'reservation_close_at' => $validated['reservation_close_at'] ?? null,
                'collection_start_at' => $validated['collection_start_at'] ?? null,
                'collection_end_at' => $validated['collection_end_at'] ?? null,
                'status' => $validated['status'] ?? 'published',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );

        $this->mealPlanService->clearDailyPlanCache($mealPlan->meal_date->toDateString());

        return response()->json([
            'message' => 'Meal plan saved successfully.',
            'data' => new MealPlanResource($mealPlan),
        ]);
    }
}
