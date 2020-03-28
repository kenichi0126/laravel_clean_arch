<?php

use Illuminate\Database\Seeder;
use Smart2\CommandModel\Eloquent\Member;

class MembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            [
                'sponsor_id' => 1,
                'family_name' => 'Switch Media Labメンバー',
                'given_name' => '(トライアル期間切れ)',
                'email' => 'trial-outside@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 2,
                'family_name' => 'Switch Media Labメンバー',
                'given_name' => '(トライアル期間中)',
                'email' => 'trial@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 3,
                'family_name' => 'Switch Media Labメンバー',
                'given_name' => '(権限なし)',
                'email' => 'trial-nopermissions@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '加藤',
                'given_name' => '隆志',
                'email' => 'kato@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '徳岡',
                'given_name' => '賢一',
                'email' => 'tokuoka@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '木下',
                'given_name' => '博文',
                'email' => 'kinoshita@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '今野',
                'given_name' => '拳',
                'email' => 'konno@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '佐藤',
                'given_name' => '慎也',
                'email' => 'sato@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
            ],
            [
                'sponsor_id' => 4,
                'family_name' => '澁谷',
                'given_name' => '翼',
                'email' => 'shibuya@switch-m.com',
                'password_digest' => bcrypt('password'),
                'started_at' => '2013-01-01',
                'login_control_flag' => 0,
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
