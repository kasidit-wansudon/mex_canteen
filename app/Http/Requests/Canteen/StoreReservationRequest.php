<?php

namespace App\Http\Requests\Canteen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'meal_plan_id' => ['required', 'integer', 'exists:meal_plans,id'],
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
