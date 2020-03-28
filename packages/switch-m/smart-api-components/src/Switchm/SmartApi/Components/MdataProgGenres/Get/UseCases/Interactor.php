<?php

namespace Switchm\SmartApi\Components\MdataProgGenres\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\MdataProgGenreDao;

class Interactor implements InputBoundary
{
    private $mdataProgGenreDao;

    private $outputBoundary;

    public function __construct(MdataProgGenreDao $mdataProgGenreDao, OutputBoundary $outputBoundary)
    {
        $this->mdataProgGenreDao = $mdataProgGenreDao;
        $this->outputBoundary = $outputBoundary;
    }

    public function __invoke(): void
    {
        $data = $this->mdataProgGenreDao->search();

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
