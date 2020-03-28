<?php

namespace Tests\Unit\App\DataAccess\SearchConditions\Delete;

use App\DataAccess\SearchConditions\Delete\DataAccess;
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
        $this->searchConditions->deleteById(1)->shouldBeCalled();
        $this->target->__invoke(1);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditions = $this->prophesize(SearchConditions::class);

        $this->target = new DataAccess($this->searchConditions->reveal());
    }
}
