<?php

namespace Switchm\SmartApi\Components\Tests\MdataProgGenres\Get\UseCases;

use Switchm\SmartApi\Components\MdataProgGenres\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\MdataProgGenres\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\MdataProgGenres\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataProgGenreDao;

class InteractorTest extends TestCase
{
    private $mdataProgGenreDao;

    private $outputBoundary;

    private $target;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->mdataProgGenreDao
            ->search()
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

        $this->mdataProgGenreDao = $this->prophesize(MdataProgGenreDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->mdataProgGenreDao->reveal(),
            $this->outputBoundary->reveal()
        );
    }
}
