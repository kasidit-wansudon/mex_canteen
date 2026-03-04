<?php

namespace App\Http\Resources\Canteen;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
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
            'period' => $this['period'],
            'date_from' => $this['date_from'],
            'date_to' => $this['date_to'],
            'reservations' => (int) $this['reservations'],
            'collected' => (int) $this['collected'],
            'no_shows' => (int) $this['no_shows'],
            'chart' => $this['chart'],
        ];
    }
}
