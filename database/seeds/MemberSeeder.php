<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\Member;

class MemberSeeder extends Seeder
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
                'id' => 1894,
                'sponsor_id' => 1,
                'family_name' => 'テスト',
                'given_name' => '太郎',
                'email' => 'test@switch-m.com',
                'login_control_flag' => 1,
                'started_at' => '2019-03-04',
            ],
        ];

        $member = new Member();
        $member->raw('SET FOREIGN_KEY_CHECKS = 0');
        $member->truncate();
        $member->raw('SET FOREIGN_KEY_CHECKS = 1');

        foreach ($list as $val) {
            $member->create($val);
        }
    }
}
