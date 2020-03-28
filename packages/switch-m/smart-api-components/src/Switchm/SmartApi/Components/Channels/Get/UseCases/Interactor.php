<?php

namespace Switchm\SmartApi\Components\Channels\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\ChannelDao;

class Interactor implements InputBoundary
{
    private $channelDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param ChannelDao $channelDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(ChannelDao $channelDao, OutputBoundary $outputBoundary)
    {
        $this->channelDao = $channelDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $data = $this->channelDao->search([
            'division' => $inputData->division(),
            'regionId' => $inputData->regionId(),
            'withCommercials' => $inputData->withCommercials(),
        ]);

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
