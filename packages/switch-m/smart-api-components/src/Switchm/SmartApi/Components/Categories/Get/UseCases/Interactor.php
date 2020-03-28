<?php

namespace Switchm\SmartApi\Components\Categories\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

class Interactor implements InputBoundary
{
    private $enqDao;

    private $outputBoundary;

    /**
     * CategoryGetCategoryInteractor constructor.
     * @param EnqDao $enqDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(EnqDao $enqDao, OutputBoundary $outputBoundary)
    {
        $this->enqDao = $enqDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $input
     */
    public function __invoke(InputData $input): void
    {
        $data = $this->enqDao->getCategory();

        $output = new OutputData($data);

        ($this->outputBoundary)($output);
    }
}
