<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\Sponsor;

class SponsorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            [
                'id' => 1,
                'name' => 'Switch Media Lab(トライアル期間切れ)',
                'status' => 'contracted',
                'started_at' => '2000-01-01',
            ],
            [
                'id' => 2,
                'name' => 'Switch Media Lab(トライアル期間中)',
                'status' => 'contracted',
                'started_at' => '2000-01-01',
            ],
            [
                'id' => 3,
                'name' => 'Switch Media Lab(権限なし)',
                'status' => 'contracted',
                'started_at' => '2000-01-01',
            ],
            [
                'id' => 4,
                'name' => 'Switch Media Lab',
                'status' => 'contracted',
                'started_at' => '2000-01-01',
            ],
        ];

        $sponsor = new Sponsor();

        $sponsor->raw('SET FOREIGN_KEY_CHECKS = 0');
        $sponsor->truncate();
        $sponsor->raw('SET FOREIGN_KEY_CHECKS = 1');

        foreach ($list as $val) {
            $sponsor->create($val);
        }
    }
}
