<?php

namespace App\Http\Resources\Canteen;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'staff_code' => $this->staff_code,
            'staff_name' => $this->staff_name,
            'email' => $this->email,
            'staff_type' => $this->staff_type,
            'role' => $this->role,
            'account_status' => (bool) $this->account_status,
            'last_login_at' => optional($this->last_login_at)->toDateTimeString(),
        ];
    }
}
