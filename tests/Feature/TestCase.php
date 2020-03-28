<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected $connectionsToTransact = [
        'smart_write_rdb',
        'smart_read_rdb',
        //'smart_dwh',
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // TODO - kinoshita: 無理やりなのでそのうち違うやり方を検討
        define('LARAVEL_START', microtime(true));
    }

    protected function setUp(): void
    {
        parent::setUp();
        \Config::set('database.default', 'smart_read_rdb');

        // TODO - kinoshita: 無理やりなのでそのうち違うやり方を検討
        factory(\Smart2\CommandModel\Eloquent\SystemInformation::class)->create([
            'name' => 'smart2-api',
        ]);
    }

    protected function actingAsTrialMember()
    {
        $member = $this->createTrialMember();

        return $this->actingAs($member);
    }

    protected function actingAsTrialOutedMember()
    {
        $member = $this->createTrialOutedMember();

        return $this->actingAs($member);
    }

    protected function actingAsMemberNoPermission()
    {
        $member = $this->createMemberNoPermission();

        return $this->actingAs($member);
    }

    protected function actingAsMember()
    {
        $member = $this->createMember();

        return $this->actingAs($member);
    }

    protected function createTrialMember()
    {
        $sponsor = factory(\Smart2\CommandModel\Eloquent\Sponsor::class)->create();

        factory(\Smart2\CommandModel\Eloquent\SponsorRole::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        factory(\Smart2\CommandModel\Eloquent\SponsorTrial::class)->create([
            'sponsor_id' => $sponsor->id,
            'settings' => [
                'started_at' => '2000-01-01',
                'ended_at' => '2999-12-31',
            ],
        ]);

        $member = factory(\Smart2\CommandModel\Eloquent\Member::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        return $member;
    }

    protected function createTrialOutedMember()
    {
        $sponsor = factory(\Smart2\CommandModel\Eloquent\Sponsor::class)->create();

        factory(\Smart2\CommandModel\Eloquent\SponsorRole::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        factory(\Smart2\CommandModel\Eloquent\SponsorTrial::class)->create([
            'sponsor_id' => $sponsor->id,
            'settings' => [
                'started_at' => '2000-01-01',
                'ended_at' => '2000-12-31',
            ],
        ]);

        $member = factory(\Smart2\CommandModel\Eloquent\Member::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        return $member;
    }

    protected function createMemberNoPermission()
    {
        $sponsor = factory(\Smart2\CommandModel\Eloquent\Sponsor::class)->create();

        factory(\Smart2\CommandModel\Eloquent\SponsorRole::class)->create([
            'sponsor_id' => $sponsor->id,
            'permissions' => [],
        ]);

        $member = factory(\Smart2\CommandModel\Eloquent\Member::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        return $member;
    }

    protected function createMember()
    {
        $sponsor = factory(\Smart2\CommandModel\Eloquent\Sponsor::class)->create();

        factory(\Smart2\CommandModel\Eloquent\SponsorRole::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        $member = factory(\Smart2\CommandModel\Eloquent\Member::class)->create([
            'sponsor_id' => $sponsor->id,
        ]);

        return $member;
    }
}
