<?php

namespace Tests\Unit\App\DataAccess\SearchConditions\Create;

use App\DataAccess\SearchConditions\Create\DataAccess;
use App\DataProxy\SearchConditions;
use Tests\TestCase;

/**
 * Class SearchConditionsTest.
 */
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
            ->create(['member_id' => 1, 'region_id' => 1, 'name' => 'test', 'route_name' => 'main.test.test', 'condition' => '{\"test\": \"test\"}'])
            ->shouldBeCalled();
        $this->target->__invoke(1, 1, 'test', 'main.test.test', '{\"test\": \"test\"}');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditions = $this->prophesize(SearchConditions::class);

        $this->target = new DataAccess($this->searchConditions->reveal());
    }
}
