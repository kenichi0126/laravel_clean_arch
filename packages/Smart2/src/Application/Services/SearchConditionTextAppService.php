<?php

namespace Smart2\Application\Services;

use Smart2\QueryModel\Service\SearchConditionTextService;

class SearchConditionTextAppService
{
    /**
     * @var SearchConditionTextService
     */
    private $searchConditionTextService;

    /**
     * @var array
     */
    private $defaultLabelSettings;

    /**
     * SearchConditionTextAppService constructor.
     * @param SearchConditionTextService $searchConditionTextService
     */
    public function __construct(SearchConditionTextService $searchConditionTextService)
    {
        $this->searchConditionTextService = $searchConditionTextService;

        $this->defaultLabelSettings = [
            'createdAt' => ['レポート作成日時:'],
            'searchPeriod' => ['検索期間:'],
            'bottom' => ['単位:'],
        ];
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRatingCsv(...$params): array
    {
        return $this->searchConditionTextService->ratingPoints(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRatingHeader(...$params): array
    {
        $data = $this->searchConditionTextService->ratingPoints(...$params);

        $header = $this->splitHeader($data);

        $this->defaultLabelSettings['searchPeriod'] = ['期間:'];

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getAdvertisingCsv(...$params): array
    {
        return $this->searchConditionTextService->getAdvertising(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getAdvertisingHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getAdvertising(...$params);

        $header = $this->splitHeader($data);

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getProgramListCsv(...$params): array
    {
        return $this->searchConditionTextService->getProgramList(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getProgramListHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getProgramList(...$params);

        $header = $this->splitHeader($data);

        $this->defaultLabelSettings['bottom'] = ['サンプル:'];

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getProgramTableHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getProgramTable(...$params);

        $header = $this->splitHeader($data);

        $this->defaultLabelSettings['bottom'] = [];

        $result = $this->make($header);

        $result['bottom'] = $result['main'];
        $result['main'] = [];

        return $result;
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getPeriodAverageCsv(...$params): array
    {
        return $this->searchConditionTextService->getPeriodAverage(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getPeriodAverageHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getPeriodAverage(...$params);

        $header = $this->splitHeader($data);

        $this->defaultLabelSettings['bottom'] = ['サンプル:', '単位:'];

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getMultiChannelProfileCsv(...$params): array
    {
        return $this->searchConditionTextService->getMultiChannelProfile(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getListHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getList(...$params);

        $header = $this->splitHeader($data);

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getListCsv(...$params): array
    {
        return $this->searchConditionTextService->getList(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getGrpHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getGrp(...$params);

        $header = $this->splitHeader($data);

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getGrpCsv(...$params): array
    {
        return $this->searchConditionTextService->getGrp(...$params);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param int $regionId
     * @param bool $isRt
     * @return array
     */
    public function getPersonalHouseholdNumbers(string $startDate, string $endDate, string $startTime, string $endTime, int $regionId, bool $isRt = true): array
    {
        return $this->searchConditionTextService->getPersonalHouseholdNumbers($startDate, $endDate, $startTime, $endTime, $regionId, $isRt);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRankingCommercialHeader(...$params): array
    {
        $data = $this->searchConditionTextService->getRankingCommercial(...$params);

        $header = $this->splitHeader($data);

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRankingCommercialCsv(...$params): array
    {
        return $this->searchConditionTextService->getRankingCommercial(...$params);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRafList(...$params): array
    {
        $data = $this->searchConditionTextService->getRaf(...$params);

        $header = $this->splitHeader($data);

        return $this->make($header);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function getRafCsv(...$params): array
    {
        return $this->searchConditionTextService->getRaf(...$params);
    }

    /**
     * @param mixed ...$params
     * @return null|string
     */
    public function getConvertedCrossConditionText(...$params): string
    {
        return $this->searchConditionTextService->getConvertedCrossConditionText(...$params);
    }

    /**
     * @param array $header
     * @param array $removeLabels
     * @return array
     */
    private function make(array $header, array $removeLabels = []): array
    {
        $result = [
            'createdAt' => [],
            'main' => [],
            'searchPeriod' => [],
            'bottom' => [],
        ];

        foreach ($header as $row) {
            $isRemove = in_array($row[0], $removeLabels);

            if (!$isRemove) {
                $this->addResult($row, $result);
            }
        }

        return $result;
    }

    /**
     * @param array $header
     * @return array
     */
    private function splitHeader(array $header): array
    {
        $result = [];
        $defaultRemoves = ['データ提供元:', '測定点:', '※注意　　本レポートのGRPは、集計期間内有効サンプルを母数に算出した集計値です。'];

        foreach ($header as $index => $row) {
            if ($index === 0) {
                continue;
            }

            if (isset($row[0]) && in_array($row[0], $defaultRemoves)) {
                continue;
            }

            if (empty($row)) {
                return $result;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @param array $row
     * @param array $result
     */
    private function addResult(array $row, array &$result): void
    {
        $target = 'main';

        if (in_array($row[0], $this->defaultLabelSettings['createdAt'])) {
            $target = 'createdAt';
        } elseif (in_array($row[0], $this->defaultLabelSettings['searchPeriod'])) {
            $target = 'searchPeriod';
        } elseif (in_array($row[0], $this->defaultLabelSettings['bottom'])) {
            $target = 'bottom';
        }
        $result[$target][] = $row;
    }
}
