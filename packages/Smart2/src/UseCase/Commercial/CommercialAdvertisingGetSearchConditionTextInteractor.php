<?php

namespace Smart2\UseCase\Commercial;

use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class CommercialAdvertisingGetSearchConditionTextInteractor
{
    private $productService;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    public function __construct(ProductService $productService, DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService)
    {
        $this->productService = $productService;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $input
     * @throws TrialException
     * @return array
     */
    public function handle(InputData $input)
    {
        if (!\Auth::getUser()->isDuringTrial($input->startDateTime(), $input->endDateTime())) {
            throw new TrialException();
        }

        $companyIds = $this->productService->getCompanyIds($input->productIds(), $input->companyIds());

        $params = [
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
        ];

        return $this->searchConditionTextAppService->getAdvertisingHeader(...array_merge($params, [
            $input->heatMapRating(),
            $input->heatMapTciPersonal(),
            $input->heatMapTciHousehold(),
        ]));
    }
}
