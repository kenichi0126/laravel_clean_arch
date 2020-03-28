<?php

namespace Tests\Feature;

use Tests\TestCase;

class SettingTest extends TestCase
{
    /**
     * @test
     */
    public function aggregateSettingIndex200(): void
    {
        list($member, $memberSystemSetting) = (function () {
            $member = $this->createMember();
            $memberSystemSetting = factory(\Smart2\CommandModel\Eloquent\MemberSystemSetting::class)->create([
                'member_id' => $member->id,
            ]);

            return [
                $member,
                $memberSystemSetting,
            ];
        })();

        $response = $this->actingAs($member)->json('GET', '/api/setting/aggregateSetting');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function aggregateSettingIndex401(): void
    {
        $response = $this->json('GET', '/api/setting/aggregateSetting');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function aggregateSettingIndex404(): void
    {
        list($member, $memberSystemSetting) = (function () {
            $member = $this->createMember();
            $memberSystemSetting = factory(\Smart2\CommandModel\Eloquent\MemberSystemSetting::class)->create([
                'member_id' => $member->id + 1,
            ]);

            return [
                $member,
                $memberSystemSetting,
            ];
        })();

        $response = $this->actingAs($member)->json('GET', '/api/setting/aggregateSetting');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function originalDivisionsIndex200(): void
    {
        list($member, $memberOriginalDiv, $division) = (function () {
            $member = $this->createMember();
            $memberOriginalDiv = factory(\Smart2\CommandModel\Eloquent\MemberOriginalDiv::class)->create([
                'member_id' => $member->id,
                'menu' => 'settings',
                'division' => 'test',
            ]);
            $memberOriginalDiv = factory(\Smart2\CommandModel\Eloquent\MemberOriginalDiv::class)->create([
                'member_id' => $member->id,
                'menu' => 'cm',
                'division' => 'test',
            ]);

            $division = factory(\Smart2\CommandModel\Eloquent\AttrDiv::class)->create([
                'division' => 'test',
                'definition' => 'occupation=8',
            ]);

            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '_def',
            ]);
            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '8',
            ]);

            return [
                $member,
                $memberOriginalDiv,
                $division,
            ];
        })();

        $response = $this->actingAs($member)->json('GET', '/api/setting/originalDivisions');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function originalDivisionsIndex401(): void
    {
        list($member, $memberOriginalDiv, $division) = (function () {
            $member = $this->createMember();
            $memberOriginalDiv = factory(\Smart2\CommandModel\Eloquent\MemberOriginalDiv::class)->create([
                'member_id' => $member->id,
                'menu' => 'settings',
                'division' => 'test',
            ]);
            $memberOriginalDiv = factory(\Smart2\CommandModel\Eloquent\MemberOriginalDiv::class)->create([
                'member_id' => $member->id,
                'menu' => 'cm',
                'division' => 'test',
            ]);

            $division = factory(\Smart2\CommandModel\Eloquent\AttrDiv::class)->create([
                'division' => 'test',
                'definition' => 'occupation=8',
            ]);

            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '_def',
            ]);
            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '8',
            ]);

            return [
                $member,
                $memberOriginalDiv,
                $division,
            ];
        })();

        $response = $this->json('GET', '/api/setting/originalDivisions');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function originalDivisionsIndex404(): void
    {
        (function (): void {
            $member = $this->createMember();

            $division = factory(\Smart2\CommandModel\Eloquent\AttrDiv::class)->create([
                'division' => 'test',
                'definition' => 'occupation=8',
            ]);

            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '_def',
            ]);
            $codes = factory(\Smart2\CommandModel\Eloquent\Code::class)->create([
                'division' => 'occupation',
                'code' => '8',
            ]);
        })();

        $response = $this->actingAsMember()->json('GET', '/api/setting/originalDivisions');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function updateSetting204(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/setting/updateSetting', [
            'secFlag' => 1,
            'division' => 'test',
        ]);

        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function updateSetting401(): void
    {
        $response = $this->json('POST', '/api/setting/updateSetting', [
            'secFlag' => 1,
            'division' => 'test',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function updateSetting405(): void
    {
        $response = $this->json('GET', '/api/setting/updateSetting', [
            'secFlag' => 1,
            'division' => 'test',
        ]);

        $response->assertStatus(405);
    }

    /**
     * @test
     */
    public function updateSetting422(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/setting/updateSetting', [
            'secFlag' => 1,
        ]);

        $response->assertStatus(422);
    }
}
