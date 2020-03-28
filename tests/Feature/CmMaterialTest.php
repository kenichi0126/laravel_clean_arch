<?php

namespace Tests\Feature;

use Tests\TestCase;

class CmMaterialTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        list(
            $product_id,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
            ]);

            return [
                $product->id,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->actingAsMember()->json('POST', '/api/cm_materials', [
            'product_ids' => [
                $product_id,
            ],
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        list(
            $product_id,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
            ]);

            return [
                $product->id,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->json('POST', '/api/cm_materials', [
            'product_ids' => [
                $product_id,
            ],
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        list(
            $product_id,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
            ]);

            return [
                $product->id,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->actingAsMember()->json('POST', '/api/cm_materials', [
            'product_ids' => [
                987654321,
            ],
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function index422(): void
    {
        list(
            $product_id,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
            ]);

            return [
                $product->id,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->actingAsMember()->json('POST', '/api/cm_materials', [
            'product_ids' => null,
            'start_date' => null,
            'end_date' => null,
        ]);

        $response->assertStatus(422);
    }
}
