<?php

namespace Tests\Feature;

use Tests\TestCase;

class CompanyNamesTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        list(
            $company,
            $commercials
        ) = (function () {
            $company = factory(\Smart2\CommandModel\Eloquent\Company::class)->create();

            $commercials = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'company_id' => $company->id,
            ]);

            return [
                $company,
                $commercials,
            ];
        })();

        $params = [
            'companyName' => $company->name,
            'startDateTime' => $commercials->started_at->format('Y-m-d H:i:s'),
            'endDateTime' => $commercials->ended_at->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAsMember()->json('POST', '/api/company_names', $params);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        $response = $this->json('POST', '/api/company_names');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/company_names', [
            'startDateTime' => '2017-12-18 16:50:00',
            'endDateTime' => '2017-12-19 16:50:00',
        ]);

        $response->assertStatus(404);
    }
}
