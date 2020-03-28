<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Get;

use App\Http\UserInterfaces\SearchConditions\Get\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\OutputData;
use Tests\TestCase;

/**
 * Class PresenterTest.
 */
final class PresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new Presenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->presenterOutput->set(arg::cetera())->shouldBeCalled();

        $this->target->__invoke(new OutputData(
            [
                [
                    'id' => 1,
                    'member_id' => 10,
                    'route_name' => 'main.test.test.1',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
                ],
                [
                    'id' => 2,
                    'member_id' => 20,
                    'route_name' => 'main.test.test.2',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
                ],
            ]
        ));
    }
}
