<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shift Guard Toggle
    |--------------------------------------------------------------------------
    | Global switch for forcing assigned shift before check-in/check-out.
    */
    'enabled' => env('ATTENDANCE_SHIFT_GUARD_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Effective Date
    |--------------------------------------------------------------------------
    | Guard is applied only at/after this datetime.
    */
    'effective_from' => env('ATTENDANCE_SHIFT_GUARD_EFFECTIVE_FROM', '2026-03-01 00:00:00'),

    /*
    |--------------------------------------------------------------------------
    | Runtime Safety
    |--------------------------------------------------------------------------
    | If true, allow attendance when data required for guard cannot be resolved.
    */
    'fail_open' => env('ATTENDANCE_SHIFT_GUARD_FAIL_OPEN', true),

    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */
    'block_message' => 'Unable to check in/out because no shift is assigned for today.',

    'auto_checkin' => [
        'enabled' => true,
        'skip_holidays' => true,
        'working_days_iso' => [1, 2, 3, 4, 5, 6], // Mon-Sat
        'check_in_time' => '09:00:00',
        'check_out_time' => '18:00:00',
        'work_plan' => 'Auto Fixed Hours',
        'remark' => 'Auto',
    ],

    'conditions' => [
        'exempt_positions' => [
            'enabled' => false,
            'position_ids' => [],
            'position_names' => [
                'REGIONAL GENERAL MANAGER',
                'SUPERVISOR COOLTHINGS',
                'MANAGEMENT COOLTHINGS',
                'PROMOTER COOLTHINGS',
            ],
        ],

        'special_regional_director_r9_non_chinese' => [
            'enabled' => false,
            'position_ids' => [],
            'position_names' => [
                'REGIONAL DIRECTOR',
            ],
            'allowed_area_ids' => [9],
            'nationality_keywords' => [
                'non-chinese',
            ],
        ],
    ],
];
