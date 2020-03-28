<?php

return [
    'list' => [
        // 新規（デフォルト有効）
        'smart2::real_time::view' => [
            'name' => 'リアルタイム',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // 新規（オプション）
        'smart2::time_shifting::view' => [
            'name' => 'タイムシフト',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // 新規（デフォルト有効）
        'smart2::region_kanto::view' => [
            'name' => 'エリア（関東）',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // 新規（オプション）
        'smart2::region_kansai::view' => [
            'name' => 'エリア（関西）',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // bs_flag                     INTEGER DEFAULT 0                                     NOT NULL
        // bs_program_flag             INTEGER DEFAULT 0                                     NOT NULL
        'smart2::bs_info::view' => [
            'name' => 'BS放送/番組表',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // overlap_flag                INTEGER DEFAULT 0                                     NOT NULL,
        'smart2::overlap_analysis::view' => [
            'name' => '重なり分析',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // cm_material_flag            INTEGER DEFAULT 0                                     NOT NULL
        'smart2::cm_materials::view' => [
            'name' => 'CM素材',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // cm_type_flag                INTEGER DEFAULT 0                                     NOT NULL
        'smart2::time_spot::view' => [
            'name' => '広告種別',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // original_div_edit_flag      INTEGER DEFAULT 0                                     NOT NULL,
        // が1
        'smart2::basic_attribute::view' => [
            'name' => '基本区分',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // original_div_edit_flag      INTEGER DEFAULT 0                                     NOT NULL,
        // が2
        'smart2::original_attribute::view' => [
            'name' => 'カスタム区分',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // condition_cross_flag        INTEGER DEFAULT 0                                     NOT NULL,
        'smart2::multiple_condition::view' => [
            'name' => '掛け合わせ条件',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // program_list_extension_flag INTEGER DEFAULT 0                                     NOT NULL,
        'smart2::program_extend::view' => [
            'name' => '番組リスト拡張',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // spot_price_flag             INTEGER DEFAULT 0                                     NOT NULL
        'smart2::spot_prices::view' => [
            'name' => 'スポット単価',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // ranking_commercial_flag             INTEGER DEFAULT 0                                     NOT NULL
        'smart2::ranking_commercial::view' => [
            'name' => 'CMランキング',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // program_target_index_kanto_flag             INTEGER DEFAULT 0                                     NOT NULL
        'smart2::program_target_index_kanto::view' => [
            'name' => '番組ターゲットインデックス関東',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],

        // program_target_index_kansai_flag             INTEGER DEFAULT 0                                     NOT NULL
        'smart2::program_target_index_kansai::view' => [
            'name' => '番組ターゲットインデックス関西',
            'options' => [
                'contact' => [
                    'start' => 'date',
                    'end' => 'date',
                ],
            ],
        ],
    ],
];
