<?php

namespace Smart2\UseCase\Program;

use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class ProgramTableGetSearchConditionTextInteractor
{
    /**
     * @var DivisionService
     */
    private $divisionService;

    /**
     * @var SampleService
     */
    private $sampleService;

    /**
     * @var SearchConditionTextAppService
     */
    private $searchConditionTextAppService;

    /**
     * ProgramPeriodAverageGetListInteractor constructor.
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     */
    public function __construct(DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService)
    {
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $input
     * @throws TrialException
     * @return array
     */
    public function handle(InputData $input): array
    {
        if (!\Auth::getUser()->isDuringTrial($input->startDateTime(), $input->endDateTime())) {
            throw new TrialException();
        }

        $cnt = 0;

        if ($input->division() === 'condition_cross') {
            $cnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDate(), $input->endDate(), $input->regionId());
        }

        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));

        $params = [
            $input->startDate(),
            $input->endDate(),
            $input->minusFiveStartTimeShort(),
            $input->minusFiveEndTimeShort(),
            $input->division(),
            $input->conditionCross(),
            $input->codes(),
            $input->regionId(),
            $cnt,
            $codeList,
            $input->searchHours(),
        ];

        return $this->searchConditionTextAppService->getProgramTableHeader(...$params);
    }
}
