<?php

namespace Smart2\UseCase\Commercial;

use Illuminate\Auth\AuthenticationException;
use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Smart2\Application\Services\UserInfoService;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class CommercialListGetSearchConditionTextInteractor
{
    private $commercialDao;

    private $rdbCommercialDao;

    private $divisionService;

    private $productService;

    private $userInfo;

    private $sampleService;

    private $searchConditionTextAppService;

    /**
     * CommercialListGetSearchConditionTextInteractor constructor.
     * @param CommercialDao $commercialDao
     * @param \Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao $rdbCommercialDao
     * @param DivisionService $divisionService
     * @param ProductService $productService
     * @param UserInfoService $userInfoService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @throws AuthenticationException
     */
    public function __construct(
        CommercialDao $commercialDao,
        \Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao $rdbCommercialDao,
        DivisionService $divisionService,
        ProductService $productService,
        UserInfoService $userInfoService,
        SampleService $sampleService,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->commercialDao = $commercialDao;
        $this->rdbCommercialDao = $rdbCommercialDao;
        $this->divisionService = $divisionService;
        $this->productService = $productService;
        $this->userInfo = $userInfoService->execute(\Auth::id());
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $input
     * @throws SampleCountException
     * @throws TrialException
     * @return array
     */
    public function handle(InputData $input): array
    {
        $this->isDuringTrial($input->startDateTime(), $input->endDateTime());
        list($cnt, $tsCnt) = $this->getConditionCrossCount(
            $input->division(),
            $input->dataType(),
            $input->conditionCross(),
            $input->startDate(),
            $input->endDate(),
            $input->regionId()
        );
        $params = $this->getParams($input);
        $header = $this->searchConditionTextAppService->getListHeader(...array_merge($params, [
            $cnt,
            $tsCnt,
        ]));
        return $header;
    }

    /**
     * @param string $startDateTime
     * @param string $endDateTime
     * @throws TrialException
     */
    public function isDuringTrial(string $startDateTime, string $endDateTime): void
    {
        if (!\Auth::getUser()->isDuringTrial($startDateTime, $endDateTime)) {
            throw new TrialException();
        }
    }

    /**
     * @param string $division
     * @param array $dataType
     * @param array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @throws SampleCountException
     * @return array
     */
    public function getConditionCrossCount(string $division, array $dataType, array $conditionCross, string $startDate, string $endDate, int $regionId): array
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($dataType);
        $cnt = 0;
        $tsCnt = 0;

        if ($division === 'condition_cross') {
            if ($isRt) {
                $cnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDate, $endDate, $regionId, true);
                // TODO: takata/マジックナンバーをConfigに移動させる
                if ($cnt < 50) {
                    throw new SampleCountException(50);
                }
            }

            if ($isTs || $isGross || $isRtTotal) {
                $tsCnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDate, $endDate, $regionId, false);

                if ($tsCnt < 50) {
                    throw new SampleCountException(50);
                }
            }
        }
        return [$cnt, $tsCnt];
    }

    /**
     * @param InputData $input
     * @return array
     */
    public function getParams(InputData $input): array
    {
        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));
        $companyIds = $this->productService->getCompanyIds($input->productIds(), $input->companyIds());
        return [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->cmType(),
            $input->cmSeconds(),
            $input->progIds(),
            $input->regionId(),
            $input->division(),
            $input->codes(),
            $input->conditionCross(),
            $companyIds,
            $input->productIds(),
            $input->cmIds(),
            $input->channels(),
            $input->order(),
            $input->page(),
            $input->dispCount(),
            $input->conv15SecFlag(),
            $input->straddlingFlg(),
            $codeList,
            $input->csvFlag(),
            $input->dataType(),
        ];
    }
}
