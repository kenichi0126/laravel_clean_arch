<?php

namespace Smart2\UseCase\Analysis;

use Smart2\Application\Exceptions\DateRangeException;
use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class AnalysisGetSearchConditionTextInteractor
{
    /**
     * @var RafDao
     */
    private $dwhAnalysisDao;

    /**
     * @var \Switchm\SmartApi\Queries\Dao\Rdb\RafDao
     */
    private $rdbAnalysisDao;

    /**
     * @var ProductService
     */
    private $productService;

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
     * @param RafDao $dwhAnalysisDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbAnalysisDao
     * @param ProductService $productService
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param AnalysisGetChartPresenter $presenter
     */
    public function __construct(RafDao $dwhAnalysisDao, \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbAnalysisDao, ProductService $productService, DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService)
    {
        $this->dwhAnalysisDao = $dwhAnalysisDao;
        $this->rdbAnalysisDao = $rdbAnalysisDao;
        $this->productService = $productService;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param AnalysisInput $input
     * @throws SampleCountException
     * @throws TrialException
     * @throws DateRangeException
     * @throws RafCsvProductAxisException
     * @return array
     */
    public function handle(InputData $input): array
    {
        if (!\Auth::getUser()->isDuringTrial($input->startDateTime(), $input->endDateTime())) {
            throw new TrialException();
        }

        $companyIds = $this->productService->getCompanyIds($input->productIds(), $input->companyIds());

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

        if (!in_array($input->division(), \Config::get('const.BASE_DIVISION'))) {
            if ($input->dateRange() > 93) {
                throw new DateRangeException(93);
            }
        }

        $params = [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->cmType(),
            $input->cmSeconds(),
            $input->regionId(),
            $input->division(),
            $input->codes(),
            $input->conditionCross(),
            $companyIds,
            $input->productIds(),
            $input->cmIds(),
            $input->channels(),
            $input->conv15SecFlag(),
            $input->progIds(),
            $input->straddlingFlg(),
            $input->dataType(),
        ];

        // 集計軸に商品を選択時、商品数を検索して30以下かどうか
        // TODO - kinoshita: string変換意味あるか？
        if ((string) $input->axisType() === \Config::get('const.AXIS_TYPE_NUMBER.PRODUCT')) {
            $productNumber = $this->rdbAnalysisDao->getProductNumbers(...$params);

            if ($productNumber->number > \Config::get('const.CSV_RAF_PRODUCT_AXIS_LIMIT')) {
                throw new RafCsvProductAxisException(\Config::get('const.CSV_RAF_PRODUCT_AXIS_LIMIT'));
            }
        }

        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));
        $csvParams = [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->cmType(),
            $input->cmSeconds(),
            $input->progIds(),
            $input->regionId(),
            $companyIds,
            $input->productIds(),
            $input->cmIds(),
            $input->channels(),
            $input->straddlingFlg(),
            $input->division(),
            $input->conditionCross(),
            $codeList,
            $cnt,
            $tsCnt,
            $input->conv15SecFlag(),
            $input->codes(),
            $input->dataType(),
            $input->csvFlag(),
            $input->period(),
        ];

        return $this->searchConditionTextAppService->getRafList(...$csvParams);
    }
}
