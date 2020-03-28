<?php

namespace Smart2\UseCase\Category;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;
use Tests\TestCase;

class CategoryGetCategoryInteractorTest extends TestCase
{
    private $enqDao;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->enqDao = $this->prophesize(EnqDao::class);
        $this->target = new CategoryGetCategoryInteractor($this->enqDao->reveal());
    }

    /**
     * @test
     */
    public function handle(): void
    {
        $this->enqDao
            ->getCategory(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $input = new CategoryGetCategoryInputData();

        $expected = [];

        $actual = $this->target->handle($input);

        $this->assertSame($expected, $actual);
    }
}
