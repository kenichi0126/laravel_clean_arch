<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChannelTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        (function (): void {
            factory(\Smart2\CommandModel\Eloquent\Channel::class, 1)->create();
        })();

        $response = $this->actingAsMember()->json('POST', '/api/channels', [
            'division' => 'dt1',
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        $response = $this->json('POST', '/api/channels', [
            'division' => 'dt1',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/channels', [
            'division' => 'XXX',
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function index422(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/channels');

        $response->assertStatus(422);
    }
}
