<?php

return [
    'CHANNEL_COLORS' => [
        1 => '#e85318',
        3 => '#fbb519',
        4 => '#36b5f7',
        5 => '#a7d546',
        6 => '#3c72a2',
        7 => '#fd7bd5',
    ],
    'CHANNEL_COLORS_KANSAI' => [
        44 => '#e85318',
        46 => '#a7d546',
        47 => '#36b5f7',
        48 => '#3c72a2',
        49 => '#fd7bd5',
        50 => '#fbb519',
    ],
    'CM_TYPE' => [
        0 => '全CM',
        1 => 'タイム',
        2 => 'スポット',
    ],
    'CM_SECONDS' => [
        1 => '全CM',
        2 => '15秒',
        3 => '30秒以上',
    ],
    'CHANNELS' => [
        '3' => '日本テレビ',
        '4' => 'テレビ朝日',
        '5' => 'TBS',
        '6' => 'テレビ東京',
        '7' => 'フジテレビ',
    ],
    'CHANNELS_KANSAI' => [
        '44' => 'NHK関西',
        '45' => 'NHKEテレ関西',
        '46' => '毎日放送',
        '47' => '朝日放送',
        '48' => 'テレビ大阪',
        '49' => '関西テレビ',
        '50' => '讀賣テレビ',
    ],
    'DIGITAL_FIVE' => '地デジ 5放送',
    'DIGITAL_FOUR' => '地デジ 4放送',
    'DIGITAL_KANTO_CM' => [
        '1' => 'NHK',
        '2' => 'Eテレ',
        '3' => '日本テレビ',
        '4' => 'テレビ朝日',
        '5' => 'TBS',
        '6' => 'テレビ東京',
        '7' => 'フジテレビ',
    ],
    'DIGITAL_KANSAI_CM' => [
        '46' => '毎日放送',
        '47' => '朝日放送',
        '48' => 'テレビ大阪',
        '49' => '関西テレビ',
        '50' => '讀賣テレビ',
    ],
    'BS_1' => [
        '15' => 'NHK BS1',
        '16' => 'NHK BSプレ',
        '17' => 'BS日テレ',
        '18' => 'BS朝日',
        '19' => 'BS-TBS',
        '20' => 'BSテレ東',
        '21' => 'BSフジ',
    ],
    'BS_2' => [
        '22' => 'WOWOWプ',
        '23' => 'スター・チャ',
        '24' => 'BS11',
        '25' => 'TwellV',
    ],
    'CONV_15_SEC_FLAG' => [
        '0' => 'しない',
        '1' => 'する',
    ],
    'REGION' => [
        '0' => '全件',
        '1' => '関東エリア',
        '2' => '関西エリア',
    ],
    'DOW' => [
        '1' => '月',
        '2' => '火',
        '3' => '水',
        '4' => '木',
        '5' => '金',
        '6' => '土',
        '0' => '日',
    ],
    'BASE_DIVISION' => [
        'ga8',
        'ga12',
        'ga10s',
        'gm',
        'oc',
    ],
    'BASE_DIVISION_MAP' => [
        'ga8' => '性・年齢8区分',
        'ga12' => '性・年齢12区分',
        'ga10s' => '男女10歳区分',
        'gm' => '男女未既婚区分',
        'oc' => '職業区分',
    ],
    'PROGRAM_TYPES' => [
        '1',
        '2',
        '3',
        '4',
        '5',
    ],
    'PROGRAM_TYPES_MAP' => [
        '1' => 'レギュラー',
        '2' => 'スペシャル',
        '3' => 'ミニ番組',
        '4' => '再放送',
        '5' => '番宣',
    ],
    'DWH_PERIOD_DATE' => 2, // Redsfhit にデータが存在するかの-日付
    'DWH_PERIOD_BOUNDARY' => 0, // Redsfhit にデータが存在するかの "時間" の 境界
    'SEARCH_PERIOD_LIMIT' => [
        'CMGRP' => [
            'BASIC' => 366,
            'CUSTOM' => 186,
        ],
        'CMLIST' => [
            'BASIC' => 366,
            'CUSTOM' => 186,
        ],
        'ADVERTISING' => [
            'BASIC' => 366,
            'CUSTOM' => 186,
        ],
        'RAF' => [
            'BASIC' => 93,
            'CUSTOM' => 93,
        ],
        'PROGRAM_LIST' => [
            'BASIC' => 366,
            'CUSTOM' => 93,
        ],
        'PROGRAM_AVERAGE' => [
            'BASIC' => 366,
            'CUSTOM' => 366,
        ],
        'RATING_POINTS' => [
            'BASIC' => 186,
            'CUSTOM' => 186,
        ],
        'OVERLAPS_TWO' => [
            'BASIC' => 93,
            'CUSTOM' => 93,
        ],
        'OVERLAPS_FIVE' => [
            'BASIC' => 93,
            'CUSTOM' => 93,
        ],
        'RANKING_CM' => [
            'BASIC' => 93,
            'CUSTOM' => 93,
        ],
    ],
    'DATA_TYPE' => [
        '0' => 'リアルタイム',
        '1' => 'タイムシフト',
        '2' => '総合視聴率',
        '3' => '延べタイムシフト',
        '4' => 'R+T7',
    ],
    'DATA_TYPE_HEADER' => [
        '0' => 'リアルタイム',
        '1' => 'タイムシフト',
        '2' => '総合',
        '3' => '延べタイムシフト',
        '4' => 'R+T7',
    ],
    'DATA_TYPE_NUMBER' => [
        'REALTIME' => 0,
        'TIMESHIFT' => 1,
        'GROSS' => 2,
        'TOTAL' => 3,
        'RT_TOTAL' => 4,
    ],
    'AXIS_TYPE' => [
        '1' => '企業別',
        '2' => '商品別',
    ],
    'AXIS_TYPE_NUMBER' => [
        'COMPANY' => '1',
        'PRODUCT' => '2',
    ],
    'RT_MIN_FROM_DATE' => [
        '1' => '2013-12-30', // 関東
        '2' => '2018-09-10', // 関西
    ],
    'TS_MIN_FROM_DATE' => [
        '1' => '2013-12-30', // 関東
        '2' => '2018-09-10', // 関西
    ],
    'TS_CALCULATED_DAYS' => [
        '1' => -99, // 関東
        '2' => -99, // 関西
    ],
    'CSV_RAF_ADVERTISING_LIMIT' => 500, // R&F:CM別のCSV出力時の限界出稿数
    'CSV_RAF_PRODUCT_AXIS_LIMIT' => 30, //R&Fで商品集計軸のcsv出力をする時、商品数の制限
    'MAX_CODE_NUMBER' => 32,
    'SAMPLE_CODE_PREFIX' => 'code',
    'SAMPLE_CODE_NUMBER_PREFIX' => 'number',
    'SAMPLE_SELECTED_PERSONAL_NAME' => 'selected_personal', // 個人選択計の属性名
    // 放送局、もしくは放送局/同放送局という企業名で、他の企業名が入らないもの
    'BROADCASTER_COMPANY_IDS' => [
        71     // 日本テレビ
        , 158  // テレビ朝日
        , 223  // TBS
        , 265  // テレビ東京
        , 318  // フジテレビ
        , 1033 // TBSラジオ/TBSラジオ&コミュニケーションズ
        , 1332 // フジテレビONE/TWO/NEXT/フジテレビ
        , 1872 // 関西テレビ/関西テレビ放送
        , 1889 // MBS/毎日放送
        , 2143 // 朝日放送
        , 2451 // TBSチャンネル/TBS
        , 2617 // TBSチャンネル/TBSテレビ
        , 2855 // 日本テレビ系列局
        , 2857 // テレビ朝日系列局
        , 2859 // TBS系列局
        , 2863 // テレビ東京系列局
        , 2865 // フジテレビ系列局
        , 2930 // テレビ大阪
        , 2939 // 毎日放送
        , 2942 // 関西テレビ
        , 3110 // TBS/TBSラジオ
        , 3213 // TBS/TBSラジオ&コミュニケーションズ
        , 3245 // テレビ東京系列
        , 4025 // TBSニュースバード/TBS
        , 4574 // よみうりテレビ/読売テレビ
        , 4826 // TBS/BS-TBS
        , 5256, // 読売テレビ
    ],
    'SMART_API_VERSION' => '1.6',
    'INIT_LOGIN_FLAG' => [
        'INITIAL' => 1,
        'ALREADY' => 0,
    ],
    'IS_RUNNING_DAILY_DATA_TRANSFER' => false,
    'RATING_POINTS_LATEST_DATE_TIME_INTERVAL' => [ // TODO - fujisaki: time_keepersに記録されているdatetimeから何秒前のデータをSMARTに表示するか
        'PER_HOURLY' => '4200',
        'PER_MINUTES' => '120',
    ],
    'SAMPLE_COUNT_MAX_NUMBER' => 50,
    'SAMPLE_TYPE_NUMBER' => [
        'BASIC' => '1',
        'CUSTOM' => '2',
        'ENQ' => '3',
    ],
    'SAMPLE_TYPE_NAME' => [
        '1' => '基本区分',
        '2' => 'カスタム区分',
        '3' => 'アンケート',
    ],
    'ENQ_PROFILE_SAMPLE_THRESHOLD' => 100,
];
