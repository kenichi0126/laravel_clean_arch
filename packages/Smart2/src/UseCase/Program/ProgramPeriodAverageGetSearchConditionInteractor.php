<?php

namespace Smart2\UseCase\Program;

use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class ProgramPeriodAverageGetSearchConditionInteractor
{
    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    public function __construct(DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService)
    {
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    public function handle(InputData $input)
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($input->dataType());

        $cnt = 0;
        $tsCnt = 0;

        if ($input->division() === 'condition_cross') {
            if ($isRt) {
                $cnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDate(), $input->endDate(), $input->regionId(), true);

                if ($cnt < 50) {
                    throw new SampleCountException(50);
                }
            }

            if ($isTs || $isGross || $isRtTotal) {
                $tsCnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDate(), $input->endDate(), $input->regionId(), false);

                if ($tsCnt < 50) {
                    throw new SampleCountException(50);
                }
            }
        }

        $sd = $input->carbonStartDateTime();
        $ed = $input->carbonEndDateTime();

        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));

        return $this->searchConditionTextAppService->getPeriodAverageHeader(
            $sd->format('Y-m-d'),
            $ed->format('Y-m-d'),
            $sd->format('Hi00'),
            $ed->format('Hi00'),
            ($input->wdays() == null) ? [] : $input->wdays(),
            ($input->holiday() === true) ? true : false,
            $input->channels(),
            $input->genres(),
            $input->programTypes(),
            $input->regionId(),
            $input->division(),
            $input->conditionCross(),
            $input->codes(),
            $input->dispAverage(),
            $codeList,
            $cnt,
            $tsCnt,
            $input->dataType(),
            $input->csvFlag()
        );
    }
}
