<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\AttrDiv;

$factory->define(AttrDiv::class, function (Faker $faker) { // 使用テーブル
    // ref https://stackoverflow.com/questions/43202886/laravel-seeding-multiple-unique-columns-with-faker
    static $division;
    static $definition;

    $count = AttrDiv::all()->count();
    // 1件もデータが存在しない場合は基底データをランダムに作成
    if ($count <= 0) {
        $defaultOption = ([
            'division' => $faker->unique()->name,
            'code' => '_def',
            'name' => $faker->unique()->name,
            'display_order' => $faker->unique()
                ->numberBetween(0, 999),
            'definition' => $faker->name,
            'color' => null,
            'population' => null,
            'weight' => null,
            'restore_info' => null,
            'restore_info_text' => null,
        ]);

        $default = [
            'personal',
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
            'household',
        ];

        if (isset($division)) {
            array_push($default, $division);
        }

        // 基本5属性, 個人, 世帯にも設定
        array_map(function ($el) use ($defaultOption): void {
            $defaultOption['division'] = $el;
            \Smart2\CommandModel\Eloquent\AttrDiv::insert($defaultOption);
        }, $default);
    }

    // ランダムに1行取得
    $row = AttrDiv::inRandomOrder()->whereNotIn('division', ['personal', 'household'])->first();

    // Generate unique imageId-languageCode combination
    return [
        'division' => $row->division,
        'code' => $faker->name,
        'name' => $row->name,
        'display_order' => $faker->unique()
            ->numberBetween(0, 999),
        'definition' => $definition ?? $definition = $faker->name,
        'color' => null,
        'population' => null,
        'weight' => null,
        'restore_info' => null,
        'restore_info_text' => null,
    ];
});
