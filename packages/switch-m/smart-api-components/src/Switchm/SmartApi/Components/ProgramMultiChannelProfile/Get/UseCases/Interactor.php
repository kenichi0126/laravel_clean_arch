<?php

namespace Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $programDao;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * ProgramMultiChannelProfileGetCsvInteractor constructor.
     * @param ProgramDao $programDao
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        ProgramDao $programDao,
        SampleService $sampleService,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->programDao = $programDao;
        $this->sampleService = $sampleService;
        $this->outputBoundary = $outputBoundary;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $inputData
     * @throws SampleCountException
     */
    public function __invoke(InputData $inputData): void
    {
        $params = [
            $inputData->isEnq(),
            $inputData->regionId(),
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->progIds(),
            $inputData->timeBoxIds(),
            $inputData->channelIds(),
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
        ];

        if ($inputData->division() === 'condition_cross') {
            $this->checkSampleCount(
                $inputData->sampleCountMaxNumber(),
                $inputData->conditionCross(),
                $inputData->startDate(),
                $inputData->endDate(),
                $inputData->regionId()
            );
        }

        $this->programDao->createMultiChannelProfileTables(...$params);
        $results = $this->programDao->getDetailMultiChannelProfileResults(
            $inputData->isEnq(),
            $inputData->channelIds(),
            $inputData->ptThreshold()
        );

        if ($inputData->division() === 'condition_cross') {
            $crossConditionText = $this->searchConditionTextAppService->getConvertedCrossConditionText(...[
                $inputData->division(),
                $inputData->conditionCross(),
            ]);

            if (isset($results[0])) {
                $results[0]->option = $crossConditionText;
            }
        }

        $selectedPrograms = $this->programDao->getSelectedProgramsForProfile();

        $personalAndHouseholdResults = $this->programDao->getHeaderProfileResults(
            $inputData->channelIds()
        );

        $header = $this->searchConditionTextAppService->getMultiChannelProfileCsv(...[
            $inputData->isEnq(),
            $inputData->regionId(),
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->channelIds(),
            $selectedPrograms,
            $inputData->sampleType(),
            $personalAndHouseholdResults,
        ]);

        $output = new OutputData($results, $inputData->startDateShort(), $inputData->endDateShort(), $header);

        ($this->outputBoundary)($output);
    }

    /**
     * @param int $sampleCountMaxNumber
     * @param null|array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @throws SampleCountException
     */
    private function checkSampleCount(int $sampleCountMaxNumber, ?array $conditionCross, string $startDate, string $endDate, int $regionId): void
    {
        $cnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDate, $endDate, $regionId, true);

        if ($cnt < $sampleCountMaxNumber) {
            throw new SampleCountException($sampleCountMaxNumber);
        }
    }
}
