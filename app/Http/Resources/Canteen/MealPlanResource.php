<?php

namespace App\Http\Resources\Canteen;

use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanResource extends JsonResource
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
            'meal_date' => optional($this->meal_date)->toDateString(),
            'meal_type' => $this->meal_type,
            'menu_items' => array_values(array_filter([
                $this->menu_item_1,
                $this->menu_item_2,
                $this->menu_item_3,
            ])),
            'reservation_open_at' => optional($this->reservation_open_at)->toDateTimeString(),
            'reservation_close_at' => optional($this->reservation_close_at)->toDateTimeString(),
            'collection_start_at' => optional($this->collection_start_at)->toDateTimeString(),
            'collection_end_at' => optional($this->collection_end_at)->toDateTimeString(),
            'status' => $this->status,
        ];
    }
}
