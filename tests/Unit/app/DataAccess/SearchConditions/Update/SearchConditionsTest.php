<?php

use App\DataAccess\SearchConditions\Update\DataAccess;
use App\DataProxy\SearchConditions;
use Tests\TestCase;

final class SearchConditionsTest extends TestCase
{
    private $target;

    private $searchConditions;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->searchConditions
            ->updateById(0, ['name' => 'test', 'condition' => '{\"test\": \"test\"}'])
            ->shouldBeCalled();
        $this->target->__invoke(0, 'test', '{\"test\": \"test\"}');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditions = $this->prophesize(SearchConditions::class);

        $this->target = new DataAccess($this->searchConditions->reveal());
    }
}
