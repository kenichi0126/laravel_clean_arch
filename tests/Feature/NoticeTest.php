<?php

namespace Tests\Feature;

use Tests\TestCase;

class NoticeTest extends TestCase
{
    /**
     * @test
     */
    public function getNotices200(): void
    {
        $response = $this->actingAsMember()->json('GET', '/api/notice/getnotices');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function getNotices401(): void
    {
        $response = $this->json('GET', '/api/notice/getnotices');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function readSystemNotice204(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readsn', [
            'notice_id' => 1,
        ]);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function readUserNotice204(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readun', [
            'notice_id' => 1,
        ]);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function readSystemNotice422_no_input(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readsn');
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function readUserNotice422_no_input(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readun');
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function readSystemNotice422_no_numeric(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readsn', [
            'notice_id' => 'a',
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function readUserNotice422_no_numeric(): void
    {
        $response = $this->actingAsMember()->json('POST', '/api/notice/readun', [
            'notice_id' => 'a',
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function readSystemNotice401(): void
    {
        $response = $this->json('POST', '/api/notice/readsn');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function readUserNotice401(): void
    {
        $response = $this->json('POST', '/api/notice/readun');

        $response->assertStatus(401);
    }
}
