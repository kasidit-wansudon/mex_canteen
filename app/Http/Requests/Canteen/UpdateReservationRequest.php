<?php

namespace App\Http\Requests\Canteen;

use Illuminate\Validation\Rule;

class UpdateReservationRequest extends StoreReservationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'reservation_type' => [
                'required',
                Rule::in(['self', 'self_invitation', 'self_pickup', 'invitation_only', 'pickup_only']),
            ],
            'visitor_count' => ['nullable', 'integer', 'min:0', 'max:30'],
            'pickup_for_staff_code' => ['nullable', 'string', 'max:40'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
