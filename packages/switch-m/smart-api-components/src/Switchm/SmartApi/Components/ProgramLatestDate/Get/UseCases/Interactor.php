<?php

namespace Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;

class Interactor implements InputBoundary
{
    private $programDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param ProgramDao $programDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(ProgramDao $programDao, OutputBoundary $outputBoundary)
    {
        $this->programDao = $programDao;
        $this->outputBoundary = $outputBoundary;
    }

    public function __invoke(): void
    {
        $data = $this->programDao->getLatestObiProgramsDate();
        $output = new OutputData((array) $data);

        ($this->outputBoundary)($output);
    }
}
