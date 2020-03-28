<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    /**
     * @test
     */
    public function login200(): void
    {
        $member = $this->createMember();

        $response = $this->json('POST', '/api/login', [
            'email' => $member->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function login403(): void
    {
        $member = $this->createTrialOutedMember();

        $response = $this->json('POST', '/api/login', [
            'email' => $member->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function login422(): void
    {
        $response = $this->json('POST', '/api/login', []);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function logout204(): void
    {
        $response = $this->actingAsMember()->json('GET', '/api/logout');

        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function logout401(): void
    {
        $response = $this->json('GET', '/api/logout');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function me204(): void
    {
        $response = $this->actingAsMember()->json('GET', '/api/me');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function me401(): void
    {
        $response = $this->json('GET', '/api/me');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function changePassword204(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => 'secret',
            'password' => 'aaaaaaaa',
            'password_confirmation' => 'aaaaaaaa',
        ]);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function changePassword401(): void
    {
        $response = $this->json('POST', '/api/change_password');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function changePassword421(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => 'secretaaaaa',
            'password' => 'aaaaaaaa',
            'password_confirmation' => 'aaaaaaaa',
        ]);
        $response->assertStatus(421);
    }

    /**
     * @test
     */
    public function changePassword422_no_input(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function changePassword422_unmatch(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => 'secret',
            'password' => 'hogehoge',
            'password_confirmation' => 'hogehoge2',
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function changePassword422_minstring(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => 'secret',
            'password' => 'hoge',
            'password_confirmation' => 'hoge',
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function changePassword422_maxstring(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/change_password', [
            'old_password' => 'secret',
            'password' => 'aaaaaaaaaabbbbbbbbbbc',
            'password_confirmation' => 'aaaaaaaaaabbbbbbbbbbc',
        ]);
        $response->assertStatus(422);
    }
}
