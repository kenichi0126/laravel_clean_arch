<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\MemberOriginalDiv;

class MemberOriginalDivSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() !== 'testing') {
            throw new Exception('This command can be executed only in the testing environment.');
        }

        $list = [
            [
                'member_id' => 1894,
                'menu' => 'cm',
                'division' => 'custom1894_2019031118221_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 0,
                'region_id' => 1,
            ],
            [
                'member_id' => 1894,
                'menu' => 'program',
                'division' => 'custom1894_2019031118221_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 0,
                'region_id' => 1,
            ],
            [
                'member_id' => 1894,
                'menu' => 'rnf',
                'division' => 'custom1894_2019031118221_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 0,
                'region_id' => 1,
            ],
            [
                'member_id' => 1894,
                'menu' => 'timezone',
                'division' => 'custom1894_2019031118221_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 0,
                'region_id' => 1,
            ],
            [
                'member_id' => 1894,
                'menu' => 'setting',
                'division' => 'custom1894_2019031118221_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 1,
                'region_id' => 1,
            ],
        ];

        $memberOriginalDiv = new MemberOriginalDiv();
        $memberOriginalDiv->raw('SET FOREIGN_KEY_CHECKS = 0');
        $memberOriginalDiv->truncate();
        $memberOriginalDiv->raw('SET FOREIGN_KEY_CHECKS = 1');

        foreach ($list as $val) {
            $memberOriginalDiv->create($val);
        }
    }
}
