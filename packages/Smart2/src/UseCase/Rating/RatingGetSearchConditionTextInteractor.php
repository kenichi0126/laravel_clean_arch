<?php

namespace Smart2\UseCase\Rating;

use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class RatingGetSearchConditionTextInteractor
{
    private $divisionService;

    private $sampleService;

    private $holidayService;

    private $ratingPoint;

    private $searchConditionTextAppService;

    public function __construct(
        DivisionService $divisionService,
        SampleService $sampleService,
        HolidayService $holidayService,
        RatingPoint $ratingPoint,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->holidayService = $holidayService;
        $this->ratingPoint = $ratingPoint;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    public function handle(RatingInput $input)
    {
        if (!\Auth::getUser()->isDuringTrial($input->startDateTime(), $input->endDateTime())) {
            throw new TrialException();
        }

        list($startDateTime, $endDateTime, $weekStartDateTime, $weekEndDateTime) = $this->ratingPoint->initDate($input->startDateTime(), $input->endDateTime(), $input->hour());

        $dateList = $this->holidayService->getDateList($weekStartDateTime, $weekEndDateTime);

        $channelIds = $this->ratingPoint->getChannelIds($input->channelType(), $input->regionId(), $input->channels());

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($input->dataType());

        $cnt = 0;
        $tsCnt = 0;

        if ($input->division() === 'condition_cross') {
            if ($isRt) {
                $cnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDateTime(), $input->endDateTime(), $input->regionId(), true);

                if ($cnt < 50) {
                    throw new SampleCountException(50);
                }
            }

            if ($isTs || $isGross || $isRtTotal) {
                $tsCnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDateTime(), $input->endDateTime(), $input->regionId(), false);

                if ($tsCnt < 50) {
                    throw new SampleCountException(50);
                }
            }
        }

        $dateList = array_filter($dateList, function ($value, $index) {
            return $index < 7;
        }, ARRAY_FILTER_USE_BOTH);
        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));

        return $this->searchConditionTextAppService->getRatingHeader(
            $input->startDateTime(),
            $input->endDateTime(),
            $input->channelType(),
            $channelIds,
            $input->division(),
            $input->code(),
            $input->dataDivision(),
            $input->displayType(),
            $input->aggregateType(),
            $input->conditionCross(),
            $cnt,
            $tsCnt,
            $input->regionId(),
            $codeList,
            $dateList,
            $input->dataType(),
            $input->csvFlag()
        );
    }
}
