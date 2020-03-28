<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Get;

use App\Http\UserInterfaces\SearchConditions\Get\Request;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputData;
use Tests\TestCase;

/**
 * Class RequestTest.
 */
final class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new Request([
            'regionId' => 1,
            'orderColumn' => 'name',
            'orderDirection' => 'desc',
        ]);
    }

    /**
     * @test
     */
    public function authorize(): void
    {
        $expected = true;
        $actual = $this->target->authorize();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function rules(): void
    {
        $expected = [
            'regionId' => 'required|integer',
            'orderColumn' => 'required',
            'orderDirection' => 'required',
        ];
        $actual = $this->target->rules();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function messages(): void
    {
        $expected = [
            'regionId.required' => 'リージョンIDは必須です。',
            'regionId.integer' => 'リージョンIDは数値です。',
            'orderColumn.required' => '順序カラム名は必須です。',
            'orderDirection.required' => '順序方向は必須です。',
        ];
        $actual = $this->target->messages();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        \Auth::shouldReceive('id')->andReturn(1)->once();
        \Auth::shouldReceive('getUser')->andReturn(new class {
            public function hasPermission()
            {
                return true;
            }
        });

        $this->target->passedValidation();

        $expected = new InputData(
            1,
            1,
            'name',
            'desc',
            true,
            true,
            true,
            true,
            true,
            true
        );

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
