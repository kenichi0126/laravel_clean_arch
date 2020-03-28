<?php

namespace Switchm\SmartApi\Components\Tests\MdataCmGenres\Get\UseCases;

use Switchm\SmartApi\Components\MdataCmGenres\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\MdataCmGenres\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\MdataCmGenres\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;

class InteractorTest extends TestCase
{
    private $mdataCmGenreDao;

    private $outputBoundary;

    private $target;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->mdataCmGenreDao
            ->getCmLargeGenres()
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $this->target->__invoke();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->mdataCmGenreDao = $this->prophesize(MdataCmGenreDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->mdataCmGenreDao->reveal(),
            $this->outputBoundary->reveal()
        );
    }
}
