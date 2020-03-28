<?php

namespace Tests\Feature;

use Tests\TestCase;

class DivisionTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        (function (): void {
            factory(\Smart2\CommandModel\Eloquent\AttrDiv::class, 1)->create();
        })();

        $response = $this->actingAsMember()->json('GET', '/api/division?menu=cm');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        $response = $this->json('GET', '/api/division');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        $response = $this->actingAsMember()->json('GET', '/api/division?menu=cm');

        $response->assertStatus(404);
    }
}
