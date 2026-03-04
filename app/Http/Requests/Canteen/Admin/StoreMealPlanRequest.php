<?php

namespace App\Http\Requests\Canteen\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMealPlanRequest extends FormRequest
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
            'meal_date' => ['required', 'date'],
            'meal_type' => ['required', Rule::in(['lunch', 'dinner'])],
            'menu_item_1' => ['required', 'string', 'max:255'],
            'menu_item_2' => ['nullable', 'string', 'max:255'],
            'menu_item_3' => ['nullable', 'string', 'max:255'],
            'reservation_open_at' => ['nullable', 'date'],
            'reservation_close_at' => ['nullable', 'date', 'after_or_equal:reservation_open_at'],
            'collection_start_at' => ['nullable', 'date'],
            'collection_end_at' => ['nullable', 'date', 'after_or_equal:collection_start_at'],
            'status' => ['nullable', Rule::in(['published', 'cancelled'])],
        ];
    }
}
