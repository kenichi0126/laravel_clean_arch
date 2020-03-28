<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProgramNamesTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        list(
            $startDate,
            $endDate,
            $channel) = (function () {
                $programs = factory(\Smart2\CommandModel\Eloquent\Programs::class)->create();

                $commercials = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'prog_id' => $programs->prog_id,
            ]);

                return [
                $programs->real_started_at,
                $programs->real_ended_at,
                $programs->channel_id,
            ];
            })();

        $response = $this->actingAsMember()->json('POST', '/api/program_names', [
            'startDateTime' => $startDate,
            'endDateTime' => $endDate,
            'channels' => $channel,
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        $response = $this->json('POST', '/api/program_names');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/program_names', [
            'startDateTime' => '2017-12-18 16:50:00',
            'endDateTime' => '2017-12-19 16:50:00',
        ]);

        $response->assertStatus(404);
    }
}
