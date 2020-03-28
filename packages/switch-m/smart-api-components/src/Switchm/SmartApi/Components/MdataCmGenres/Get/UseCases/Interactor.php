<?php

namespace Switchm\SmartApi\Components\MdataCmGenres\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;

class Interactor implements InputBoundary
{
    private $mdataCmGenreDao;

    private $outputBoundary;

    public function __construct(MdataCmGenreDao $mdataCmGenreDao, OutputBoundary $outputBoundary)
    {
        $this->mdataCmGenreDao = $mdataCmGenreDao;
        $this->outputBoundary = $outputBoundary;
    }

    public function __invoke(): void
    {
        $data = $this->mdataCmGenreDao->getCmLargeGenres();

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
