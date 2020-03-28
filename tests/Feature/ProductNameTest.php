<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductNameTest extends TestCase
{
    /**
     * @test
     */
    public function index200(): void
    {
        list(
            $company_id,
            $product_name,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
                'company_id' => $product->company_id,
            ]);

            return [
                $product->company_id,
                $product->name,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $params = [
            'companyIds' => [
                $company_id,
            ],
            'productName' => $product_name,
            'startDateTime' => $start_date->format('Y-m-d'),
            'endDateTime' => $end_date->format('Y-m-d'),
            'regionIds' => [1],
        ];

        $response = $this->actingAsMember()->json('POST', '/api/product_names', $params);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function index401(): void
    {
        list(
            $company_id,
            $product_name,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
                'company_id' => $product->company_id,
            ]);

            return [
                $product->company_id,
                $product->name,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $params = [
            'companyIds' => [
                $company_id,
            ],
            'productName' => $product_name,
            'startDateTime' => $start_date->format('Y-m-d'),
            'endDateTime' => $end_date->format('Y-m-d'),
            'regionIds' => [1],
        ];

        $response = $this->json('POST', '/api/product_names', $params);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function index404(): void
    {
        list(
            $company_id,
            $product_name,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create([
                'name' => 'XXX',
                'company_id' => '123',
            ]);

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
                'company_id' => $product->company_id,
            ]);

            return [
                $product->company_id,
                $product->name,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->actingAsMember()->json('POST', '/api/product_names', [
            'companyIds' => [
                321,
            ],
            'productName' => 'ZZZ',
            'startDateTime' => $start_date->format('Y-m-d'),
            'endDateTime' => $start_date->format('Y-m-d'),
            'regionIds' => [1],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function index422(): void
    {
        list(
            $company_id,
            $product_name,
            $start_date,
            $end_date
        ) = (function () {
            $product = factory(\Smart2\CommandModel\Eloquent\Product::class)->create();

            $commercial = factory(\Smart2\CommandModel\Eloquent\Commercial::class)->create([
                'product_id' => $product->id,
                'company_id' => $product->company_id,
            ]);

            return [
                $product->company_id,
                $product->name,
                $commercial->started_at,
                $commercial->ended_at,
            ];
        })();

        $response = $this->actingAsMember()->json('POST', '/api/product_names', [
            'company_ids' => null,
            'product_name' => null,
            'start_date' => null,
            'end_date' => null,
        ]);

        $response->assertStatus(422);
    }
}
