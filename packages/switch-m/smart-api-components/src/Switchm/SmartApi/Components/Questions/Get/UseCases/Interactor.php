<?php

namespace Switchm\SmartApi\Components\Questions\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

class Interactor implements InputBoundary
{
    private $enqDao;

    private $outputBoundary;

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
        $params = [
            $input->keyWord(),
            $input->qGroup(),
            $input->tag(),
        ];

        $data = $this->enqDao->getQuestion(...$params);

        $output = new OutputData($data);

        ($this->outputBoundary)($output);
    }
}
