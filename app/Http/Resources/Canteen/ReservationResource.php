<?php

namespace App\Http\Resources\Canteen;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reservation_date' => optional($this->reservation_date)->toDateString(),
            'meal_type' => $this->meal_type,
            'reservation_type' => $this->reservation_type,
            'visitor_count' => (int) $this->visitor_count,
            'pickup_for_staff_code' => $this->pickup_for_staff_code,
            'meal_count' => (int) $this->meal_count,
            'qr_code_token' => $this->qr_code_token,
            'qr_expiry_time' => optional($this->qr_expiry_time)->toDateTimeString(),
            'status' => $this->status,
            'remark' => $this->remark,
            'is_editable' => $this->isEditable(),
            'meal_plan' => new MealPlanResource($this->whenLoaded('mealPlan')),
            'user' => new UserResource($this->whenLoaded('user')),
            'visitor' => new VisitorResource($this->whenLoaded('visitor')),
            'meal_collection' => $this->whenLoaded('mealCollection', function () {
                return [
                    'collected_at' => optional($this->mealCollection->collected_at)->toDateTimeString(),
                    'collector_staff_code' => $this->mealCollection->collector_staff_code,
                    'collector_name' => $this->mealCollection->collector_name,
                ];
            }),
        ];
    }
}
