<?php

namespace App\Http\Requests\Canteen\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
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
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'staff_type' => ['nullable', 'in:jumbo,latam'],
            'staff_code' => ['nullable', 'string', 'max:40'],
            'staff_name' => ['nullable', 'string', 'max:190'],
            'email' => ['nullable', 'string', 'max:190'],
            'reservation_status' => ['nullable', 'string', 'max:50'],
            'period' => ['nullable', 'in:day,week,month'],
            'export' => ['nullable', 'in:csv'],
            'week_start' => ['nullable', 'date'],
        ];
    }
}
