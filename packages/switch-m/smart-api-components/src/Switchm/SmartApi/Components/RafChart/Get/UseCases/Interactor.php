<?php

namespace Switchm\SmartApi\Components\RafChart\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Interactor implements InputBoundary
{
    /**
     * @var RafDao
     */
    private $dwhRafDao;

    /**
     * @var \Switchm\SmartApi\Queries\Dao\Rdb\RafDao
     */
    private $rdbRafDao;

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
     * @var OutputBoundary
     */
    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param RafDao $dwhRafDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbRafDao
     * @param ProductService $productService
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(RafDao $dwhRafDao, \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbRafDao, ProductService $productService, DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService, OutputBoundary $outputBoundary)
    {
        $this->dwhRafDao = $dwhRafDao;
        $this->rdbRafDao = $rdbRafDao;
        $this->productService = $productService;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $input
     * @throws RafCsvProductAxisException
     * @throws SampleCountException
     */
    public function __invoke(InputData $input): void
    {
        $companyIds = $this->productService->getCompanyIds($input->productIds(), $input->companyIds());

        $cnt = 0;
        $tsCnt = 0;

        if ($input->division() === 'condition_cross') {
            $dataTypes = [];

            if ($input->dataTypeFlags()['isRt']) {
                $dataTypes[] = true;
            }

            if ($input->dataTypeFlags()['isTs'] || $input->dataTypeFlags()['isGross'] || $input->dataTypeFlags()['isRtTotal']) {
                $dataTypes[] = false;
            }

            foreach ($dataTypes as $val) {
                $cnt = $this->sampleService->getConditionCrossCount($input->conditionCross(), $input->startDate(), $input->endDate(), $input->regionId(), $val);

                if ($cnt < 50) {
                    throw new SampleCountException(50);
                }
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
        if ($input->axisType() === $input->axisTypeProduct()) {
            $productNumber = $this->rdbRafDao->getProductNumbers(...$params);

            if ($productNumber->number > $input->productAxisLimit()) {
                throw new RafCsvProductAxisException($input->productAxisLimit());
            }
        }
        $codeNames = $input->codeNames();

        if ($input->division() == 'condition_cross') {
            $codeNames = [
                [
                    'code' => 'condition_cross',
                    'name' => '掛け合わせ条件',
                ],
                [
                    'code' => 'household',
                    'name' => '世帯',
                ],
            ];
        }

        $this->dwhRafDao->commonCreateTempTables(...array_merge($params, [
            '0',
            '0',
            'cm',
            $input->dataTypeFlags(),
            $input->axisTypeProduct(),
            $input->axisTypeCompany(),
        ]));
        $results = $this->dwhRafDao->getChartResults(...array_merge($params, [
            $input->reachAndFrequencyGroupingUnit(),
            $input->dataTypeFlags(),
        ]));

        if (count($results) === 0) {
            // TODO - konno: ModelNotFoundException
            throw new NotFoundHttpException();
        }
        $series = $this->createSeries($results, $codeNames, $input->reachAndFrequencyGroupingUnit());

        // csvボタン作成用
        $csvButtonInfo = $this->dwhRafDao->getCsvButtonInfo(...array_merge($params, [
            $input->axisType(),
            $input->channelAxis(),
            $input->axisTypeProduct(),
            $input->axisTypeCompany(),
        ]));

        $output = new OutputData(
            $series,
            $this->getCategories($codeNames, $results),
            $this->getColumnData($codeNames, $results, 'average'),
            $this->getColumnData($codeNames, $results, 'over_one'),
            end($series)['data'],
            $csvButtonInfo,
            $this->searchConditionTextAppService->getRafList(...$this->getSearchConditionParams($input, $cnt, $tsCnt))
        );

        ($this->outputBoundary)($output);
    }

    private function createSeries(array $results, array $codeNames, array $reachAndFrequencyGroupingUnit = null): array
    {
        // freq_0 の計算
        foreach ($results as $key => $val) {
            $sum = 0;

            foreach ($val as $name => $value) {
                if (preg_match('/^freq_(\d+)$/', $name)) {
                    $sum += $value;
                }
            }
            $results[$key]->freq_0 = round(100 - $sum, 1);
        }

        $units = [[0, 0]];
        $before = 0;

        foreach ($reachAndFrequencyGroupingUnit as $n) {
            $units[] = [$before + 1, $n];
            $before = $n;
        }
        $units[] = $before + 1;

        $names = [];

        foreach ($units as $i => $unit) {
            if (is_array($unit) && $unit[0] == $unit[1]) {
                $names[] = $unit[0] . '回';
            } elseif (is_array($unit)) {
                $names[] = sprintf('%d回〜%d回', $unit[0], $unit[1]);
            } else {
                $names[] = sprintf('%d回以上', $unit);
            }
        }
        $series = [];

        foreach ($names as $i => $name) {
            $series[] = [
                'type' => 'column',
                'name' => $name,
                'yAxis' => 0,
                'data' => $this->getColumnData($codeNames, $results, 'freq_' . $i),
                'tooltip' => [
                    'valueSuffix' => ' %',
                ],
            ];
        }

        $series = array_reverse($series); // 表示順のため
        $series[] = [
            'type' => 'line',
            'name' => 'GRP',
            'yAxis' => 1,
            'data' => $this->getSplineData($codeNames, $results),
        ];
        return $series;
    }

    private function getColumnData(array $codeNames, array $reach, string $column): array
    {
        $result = [];

        foreach ($codeNames as $code) {
            foreach ($reach as $row) {
                if ($code['code'] == $row->code) {
                    $vars = get_object_vars($row);
                    array_push($result, (float) ($vars[$column]));
                    break;
                }
            }
        }
        return $result;
    }

    private function getSplineData(array $codeNames, array $grp): array
    {
        $result = [];

        foreach ($codeNames as $code) {
            foreach ($grp as $row) {
                if ($code['code'] == $row->code) {
                    array_push($result, (float) ($row->grp));
                    break;
                }
            }
        }
        return $result;
    }

    private function getCategories(array $codeNames, array $results): array
    {
        $categories = [];

        foreach ($codeNames as $code) {
            foreach ($results as $result) {
                if ($result->code === $code['code']) {
                    array_push($categories, $code['name']);
                    break;
                }
            }
        }
        return $categories;
    }

    private function getSearchConditionParams(InputData $input, int $conditionCrossCount, int $tsConditionCrossCount): array
    {
        $codeList = $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), $input->userId(), $input->baseDivision());
        return [
            $input->startDate(),
            $input->endDate(),
            $input->startTime(),
            $input->endTime(),
            $input->cmType(),
            $input->cmSeconds(),
            $input->progIds(),
            $input->regionId(),
            $input->companyIds(),
            $input->productIds(),
            $input->cmIds(),
            $input->channels(),
            $input->straddlingFlg(),
            $input->division(),
            $input->conditionCross(),
            $codeList,
            $conditionCrossCount,
            $tsConditionCrossCount,
            $input->conv15SecFlag(),
            $input->codes(),
            $input->dataType(),
            $input->csvFlag(),
            $input->period(),
        ];
    }
}
