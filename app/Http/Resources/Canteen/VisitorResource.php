<?php

namespace App\Http\Resources\Canteen;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
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
            'visitor_code' => $this->visitor_code,
            'visitor_name' => $this->visitor_name,
            'email' => $this->email,
            'valid_from' => optional($this->valid_from)->toDateString(),
            'valid_until' => optional($this->valid_until)->toDateString(),
            'account_status' => (bool) $this->account_status,
        ];
    }
}
