<?php

namespace Smart2\UseCase\Program;

use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class ProgramListGetSearchConditionInteractor
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
        if (!\Auth::getUser()->isDuringTrial($input->startDateTime(), $input->endDateTime())) {
            throw new TrialException();
        }

        // 放送
        $channels = null;
        $bsFlg = false;

        if ($input->digitalAndBs() === 'digital') {
            $channels = $input->digitalKanto();
        } elseif ($input->digitalAndBs() === 'bs1') {
            $channels = $input->bs1();
            $bsFlg = true;
        } elseif ($input->digitalAndBs() === 'bs2') {
            $channels = $input->bs2();
            $bsFlg = true;
        }

        $page = 0;

        $isHoliday = ($input->holiday() === true) ? true : false;

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

        $params = [
            $input->startDate(),
            $input->endDate(),
            $input->minusFiveStartTimeShort(),
            $input->minusFiveEndTimeShort(),
            ($input->wdays() == null) ? [] : $input->wdays(),
            $isHoliday,
            $channels,
            $input->genres(),
            $input->programNames(),
            $input->division(),
            $input->conditionCross(),
            $input->codes(),
            $input->order(),
            $input->dispCount(),
            $input->regionId(),
            $page,
            $input->straddlingFlg(),
            $bsFlg,
            $input->csvFlag(),
            \Auth::getUser()->hasPermission('smart2::program_extend::view') && $isRt,
            $input->dataType(),
        ];

        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));

        // 表示用の時刻に書き換える。
        $params[2] = $input->startTimeShort();
        $params[3] = $input->endTimeShort();

        return $this->searchConditionTextAppService->getProgramListHeader(...array_merge($params, [
            $cnt,
            $tsCnt,
            $input->digitalAndBs(),
            $codeList,
        ]));
    }
}
