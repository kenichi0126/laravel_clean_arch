<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\SystemInformation;

class SystemInformationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemInformation = new SystemInformation();

        $systemInformation->raw('SET FOREIGN_KEY_CHECKS = 0');
        $systemInformation->truncate();
        $systemInformation->raw('SET FOREIGN_KEY_CHECKS = 1');

        $list = [
            [
                'name' => 'smart2-api',
            ],
            [
                'name' => 'canopus-api',
            ],
        ];

        foreach ($list as $val) {
            factory(SystemInformation::class)->create($val);
        }
    }
}
