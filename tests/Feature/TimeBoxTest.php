<?php

namespace Tests\Feature;

use Tests\TestCase;

class TimeBoxTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        (function (): void {
            factory(\Smart2\CommandModel\Eloquent\TimeBox::class)->create();
        })();

        $response = $this->actingAsMember()->json('GET', '/api/timebox/latest');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        $response = $this->json('GET', '/api/timebox/latest');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        $response = $this->actingAsMember()->json('GET', '/api/timebox/latest');

        $response->assertStatus(404);
    }
}
