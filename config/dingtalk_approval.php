<?php
// config/dingtalk_approval.php
// immediate_leader: direct manager
// upper_leader: next level manager
// upper_or_dept: upper leader or department head if no upper leader
// dept_or_upper: department head or upper leader if no department head
// hr_regional_assistant: HR regional assistant based on province
// rgm_by_province: RGM based on province

$backofficeDeptFlow = [
  'approvers' => [
    ['type' => 'upper_or_dept'],
    ['type' => 'staff_code', 'value' => 'R93203'],
  ],
  'cc' => [],
];

return [
  'test_mode' => (bool) env('DINGTALK_APPROVAL_TEST_MODE', false),

  'test_approvers' => env('DINGTALK_APPROVAL_TEST_APPROVERS', ''),

  'test_cc_list' => env('DINGTALK_APPROVAL_TEST_CC_LIST', ''),

  'area' => [
    'constants' => [
      'jimmy' => 'R93475',
      'hr_hq_approval' => 'R93518',
      'approve_r93152' => 'R93152',
      'cc_hq_hr' => 'R93402',
      'cc_hq_admin' => 'R93125',
      'cc_r93151' => 'R93151',
      'cc_1003' => '1003',
      'r93246' => 'R93246',
    ],
    'leave_type_flows' => [
      'get_upper_leader' => [
        'approvers' => [
          ['type' => 'upper_leader'],
        ]
      ],
      9 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          ['type' => 'staff_code', 'value' => 'R93518'],
        ],
        'cc' => [],
      ],
      19 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'staff_code', 'value' => 'R93475'],
        ],
        'cc' => [
          ['type' => 'staff_code', 'value' => 'R93402'],
          ['type' => 'staff_code', 'value' => 'R93125'],
        ],
      ],
      20 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          // ['type' => 'staff_code', 'value' => 'R93518'],
        ],
        'cc' => [],
      ],
      24 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
        ],
        'cc' => [
          ['type' => 'staff_code', 'value' => 'R93402'],
          ['type' => 'staff_code', 'value' => '1003'],
          ['type' => 'staff_code', 'value' => 'R93151'],
        ],
      ],
      25 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
        ],
        'cc' => [
          ['type' => 'staff_code', 'value' => 'R93402'],
          ['type' => 'staff_code', 'value' => '1003'],
          ['type' => 'staff_code', 'value' => 'R93151'],
        ],
      ],
      26 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          ['type' => 'staff_code', 'value' => 'R93152'],
        ],
        'cc' => [],
      ],
      27 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          ['type' => 'staff_code', 'value' => 'R93152'],
        ],
        'cc' => [],
      ],
      28 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          ['type' => 'staff_code', 'value' => 'R93518'],
        ],
        'cc' => [],
      ],
      29 => [
        'approvers' => [
          ['type' => 'upper_leader'],
        ],
        'cc' => [],
      ],
      31 => [
        'approvers' => [
          ['type' => 'upper_leader'],
          ['type' => 'hr_regional_assistant'],
          ['type' => 'staff_code', 'value' => 'R93152'],
        ],
        'cc' => [],
      ],
      // AdjustTime
      32 => [
        'approvers' => [
          ['type' => 'upper_leader'],
        ],
        'cc' => [],
      ],
    ],
    'position_overrides' => [
      // Mapping based on sheet [area- position]

      // REGIONAL GENERAL MANAGER
      87 => [
        'upper_leader' => ['type' => 'staff_code', 'value' => 'R93475'],
        'flows' => [
          'china' => [
            // RGM (chinese): EVP(jimmy) + CC HR/Admin for most leave types (sheet leaves Mexican annual blank)
            '_default' => [
              'approvers' => [
                ['type' => 'staff_code', 'value' => 'R93475'],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => 'R93125'],
              ],
            ],
          ],
          'mex' => [
            // RGM (mexican): paid other requires "... > RGM > CC" (we will exclude self in resolver)
            24 => [
              'approvers' => [
                ['type' => 'upper_leader'],
                ['type' => 'hr_regional_assistant'],
                ['type' => 'province_position', 'value' => 87],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => '1003'],
                ['type' => 'staff_code', 'value' => 'R93151'],
              ],
            ],
          ],
        ],
      ],

      // REGIONAL DIRECTOR
      88 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
        'flows' => [
          'china' => [
            19 => [
              'approvers' => [
                ['type' => 'upper_leader'],
                ['type' => 'staff_code', 'value' => 'R93475'],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => 'R93125'],
              ],
            ],
            '_default' => [
              'approvers' => [
                ['type' => 'upper_leader'],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => 'R93125'],
              ],
            ],
          ],
          'mex' => [
            24 => [
              'approvers' => [
                ['type' => 'upper_leader'],
                ['type' => 'hr_regional_assistant'],
                ['type' => 'province_position', 'value' => 87],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => '1003'],
                ['type' => 'staff_code', 'value' => 'R93151'],
              ],
            ],
            25 => [
              'approvers' => [
                ['type' => 'upper_leader'],
                ['type' => 'hr_regional_assistant'],
                ['type' => 'province_position', 'value' => 87],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => '1003'],
                ['type' => 'staff_code', 'value' => 'R93151'],
              ],
            ],
          ],
        ],
      ],

      // SUBDIRECTOR / SUBDIRECTOR SR: upper leader is RGM
      89 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
      ],
      164 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
      ],

      // DISTRIBUTOR MANAGER: upper leader is RGM
      90 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
      ],

      // REGIONAL ASSISTANT: upper leader is RD
      91 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // HUNTER LEADER: upper leader is RD
      92 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // HUNTER: upper leader is HUNTER LEADER -> chagne from HUNTER LEADER to RD
      93 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // SUPERVISOR: upper leader is RD
      94 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // PROMOTER: upper leader is SUPERVISOR
      95 => [
        'upper_leader' => ['type' => 'subarea_position', 'value' => [94, 93]],
      ],

      // TRAINER: upper leader is RD
      96 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // SALES ASSISTANT (Chinese): upper leader is RGM; special flow for Chinese annual
      118 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
        'flows' => [
          'china' => [
            19 => [
              'approvers' => [
                ['type' => 'upper_leader'],
                ['type' => 'staff_code', 'value' => 'R93475'],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => 'R93125'],
              ],
            ],
            '_default' => [
              'approvers' => [
                ['type' => 'upper_leader'],
              ],
              'cc' => [
                ['type' => 'staff_code', 'value' => 'R93402'],
                ['type' => 'staff_code', 'value' => 'R93125'],
              ],
            ],
          ],
        ],
      ],

      // HR REGIONAL ASSISTANT: flow does not include HR REGIONAL ASSISTANT step
      306 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // ACTIVADOR / ACTIVADOR LEADER -> RD
      210 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      215 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 88],
      ],

      // KAM JR.
      240 => [
        'upper_leader' => ['type' => 'province_position', 'value' => 87],
      ],

      // OBS PROMOTER
      243 => [
        'upper_leader' => ['type' => 'subarea_position', 'value' => [315]],
        '_map' => [
          24 => '24_25',
          25 => '24_25',
          26 => '26_27_31',
          27 => '26_27_31',
          31 => '26_27_31',
        ],
        'flows' => [
          9 => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'MRH14779'],
              ['type' => 'staff_code', 'value' => 'R93518'],
            ],
            'cc' => [],
          ],
          28 => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93518'],
            ],
            'cc' => [],
          ],
          // [24, 25] => [
          '24_25' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [
              ['type' => 'staff_code', 'value' => 'R93402'],
              ['type' => 'staff_code', 'value' => '1003'],
              ['type' => 'staff_code', 'value' => 'R93151'],
            ],
          ],
          // [26, 27, 31] => [
          '26_27_31' => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93152'],
            ],
            'cc' => [],
          ],
          '_default' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [],
          ],
        ],
      ],
      // KIOSKO PROMOTER
      318 => [
        'upper_leader' => ['type' => 'staff_code', 'value' => 'R43468'],
        '_map' => [
          9 => '9_28',
          24 => '24_25',
          25 => '24_25',
          26 => '26_27_31',
          27 => '26_27_31',
          28 => '9_28',
          31 => '26_27_31',
        ],
        'flows' => [
          '9_28' => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93518'],
            ],
            'cc' => [],
          ],
          '24_25' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [
              ['type' => 'staff_code', 'value' => 'R93402'],
              ['type' => 'staff_code', 'value' => '1003'],
              ['type' => 'staff_code', 'value' => 'R93151'],
            ],
          ],
          '26_27_31' => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93152'],
            ],
            'cc' => [],
          ],
          '_default' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [],
          ],
        ],
      ],
      // STORE MANAGER: 
      315 => [
        'upper_leader' => ['type' => 'staff_code', 'value' => 'MRH14779'],
        '_map' => [
          24 => '24_25',
          25 => '24_25',
          26 => '26_27_31',
          27 => '26_27_31',
          31 => '26_27_31',
        ],
        'flows' => [
          9 => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93518'],
            ],
            'cc' => [],
          ],
          // [24, 25] => [
          '24_25' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [
              ['type' => 'staff_code', 'value' => 'R93402'],
              ['type' => 'staff_code', 'value' => '1003'],
              ['type' => 'staff_code', 'value' => 'R93151'],
            ],
          ],
          // [26, 27, 31] => [
          '26_27_31' => [
            'approvers' => [
              ['type' => 'upper_leader'],
              ['type' => 'staff_code', 'value' => 'R93152'],
            ],
            'cc' => [],
          ],
          '_default' => [
            'approvers' => [
              ['type' => 'upper_leader'],
            ],
            'cc' => [],
          ],
        ],
      ],

    ],
  ],

  'backoffice' => [
    'leave_type_flows' => array_replace(
      array_fill_keys([9, 22, 23, 24, 25, 26, 27, 31, 28], $backofficeDeptFlow),
      [
        // Chinese Annual Leave
        19 => [
          'approvers' => [
            ['type' => 'upper_or_dept'],
          ],
          'cc' => [
            ['type' => 'staff_code', 'value' => 'R93402'],
            ['type' => 'staff_code', 'value' => 'R93125'],
          ],
        ],

        // Mexican Annual Leave
        20 => [
          'approvers' => [
            ['type' => 'upper_or_dept'],
            ['type' => 'staff_code', 'value' => 'R93203'],
          ],
          'cc' => [],
        ],
        29 => [
          'approvers' => [
            ['type' => 'upper_or_dept'],
          ],
          'cc' => [],
        ],
        // AdjustTime
        32 => [
          'approvers' => [
            ['type' => 'upper_or_dept'],
          ],
          'cc' => [],
        ],
        // Overtime
        33 => [
          'approvers' => [
            ['type' => 'staff_code', 'value' => 'R33333'],     // department approval
            ['type' => 'staff_code', 'value' => 'R93358'],     // next manager
            ['type' => 'staff_code', 'value' => 'R93151'],     // HR approval
          ],
        ],
        // Non Working Day Work
        35 => [
          'approvers' => [
            ['type' => 'staff_code', 'value' => 'R33333'],     // department approval
            ['type' => 'staff_code', 'value' => 'R93358'],     // next manager
            ['type' => 'staff_code', 'value' => 'R93151'],     // HR approval
          ],
        ],
      ]
    ),
  ],
  'leave_types_flow_ids' => [31, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 27, 16, 15, 14, 13, 12, 11, 10, 9],
  'leader_bypass' => [
    'approvers' => [
      // If upper leader is RD, bypass to RGM
      ['type' => 'province_position', 'value' => 88],
      ['type' => 'province_position', 'value' => 87],
      ['type' => 'staff_code', 'value' => 'MRH17580'],
    ],
  ],
];
