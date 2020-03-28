<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\SponsorTrial;

class SponsorTrialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsorTrial = new SponsorTrial();

        $sponsorTrial->raw('SET FOREIGN_KEY_CHECKS = 0');
        $sponsorTrial->truncate();
        $sponsorTrial->raw('SET FOREIGN_KEY_CHECKS = 1');

        $data = [
            // Switch Media Lab(トライアル期間切れ)
            [
                'sponsor_id' => 1,
                'settings' => [
                    'started_at' => '2000-01-01',
                    'ended_at' => '2000-03-01',
                ],
            ],

            // Switch Media Lab(トライアル期間中)
            ['sponsor_id' => 2],
        ];

        foreach ($data as $val) {
            factory(\Smart2\CommandModel\Eloquent\SponsorTrial::class)->create($val);
        }
    }
}
