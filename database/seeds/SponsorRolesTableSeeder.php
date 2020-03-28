<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\SponsorRole;

class SponsorRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsorRole = new SponsorRole();

        $sponsorRole->raw('SET FOREIGN_KEY_CHECKS = 0');
        $sponsorRole->truncate();
        $sponsorRole->raw('SET FOREIGN_KEY_CHECKS = 1');

        $data = [
            // Switch Media Lab(トライアル期間切れ)
            [
                'sponsor_id' => 1,
            ],

            // Switch Media Lab(トライアル期間中)
            [
                'sponsor_id' => 2,
            ],

            // Switch Media Lab(権限なし)
            [
                'sponsor_id' => 3,
                'permissions' => [],
            ],

            // switch通常
            [
                'sponsor_id' => 4,
            ],
        ];

        foreach ($data as $val) {
            factory(\Smart2\CommandModel\Eloquent\SponsorRole::class)->create($val);
        }
    }
}
