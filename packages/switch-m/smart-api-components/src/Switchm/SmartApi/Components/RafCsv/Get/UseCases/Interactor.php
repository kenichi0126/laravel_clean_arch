<?php

namespace Switchm\SmartApi\Components\RafCsv\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Smart2\QueryModel\Service\SearchConditionTextService;
use stdClass;
use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;
use Traversable;

class Interactor implements InputBoundary
{
    use OutputCsvTrait;

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
     * @var SearchConditionTextService
     */
    private $searchConditionTextService;

    /**
     * @var SearchConditionTextAppService
     */
    private $searchConditionTextAppService;

    private $outputBoundary;

    private $timeGroupList = [
        '05:00～07:59',
        '08:00～11:59',
        '12:00～17:59',
        '18:00～22:59',
        '23:00～23:59',
        '24:00～28:59',
    ];

    private $descriptions = [
        'ga8' => [
            'c' => '4〜12才男女',
            't' => '13〜19才男女',
            'f1' => '女20〜34才',
            'f2' => '女35〜49才',
            'f3' => '女50才以上',
            'm1' => '男20〜34才',
            'm2' => '男35〜49才',
            'm3' => '男50才以上',
        ],
        'ga10s' => [
            'f10s' => '女4〜19才',
            'f20s' => '女20〜29才',
            'f30s' => '女30〜39才',
            'f40s' => '女40〜49才',
            'f50s' => '女50〜59才',
            'f60s' => '女60才以上',
            'm10s' => '男4〜19才',
            'm20s' => '男20〜29才',
            'm30s' => '男30〜39才',
            'm40s' => '男40〜49才',
            'm50s' => '男50〜59才',
            'm60s' => '男60才以上',
        ],
        'ga12' => [
            'fc' => '女4〜12才',
            'ft' => '女13〜19才',
            'f1' => '女20〜34才',
            'f2' => '女35〜49才',
            'f3y' => '女50〜64才',
            'f3o' => '女65才以上',
            'mc' => '男4〜12才',
            'mt' => '男13〜19才',
            'm1' => '男20〜34才',
            'm2' => '男35〜49才',
            'm3y' => '男50〜64才',
            'm3o' => '男65才以上',
        ],
        'gm' => [
            'c' => '4〜12才男女',
            't' => '13〜19才男女',
            'fs' => '20才以上未婚女性',
            'fm' => '20才以上既婚女性',
            'ms' => '20才以上未婚男性',
            'mm' => '20才以上既婚男性',
        ],
    ];

    /**
     * Interactor constructor.
     * @param RafDao $dwhRafDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbRafDao
     * @param ProductService $productService
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     * @param SearchConditionTextService $searchConditionTextService
     */
    public function __construct(RafDao $dwhRafDao, \Switchm\SmartApi\Queries\Dao\Rdb\RafDao $rdbRafDao, ProductService $productService, DivisionService $divisionService, SampleService $sampleService, SearchConditionTextAppService $searchConditionTextAppService, SearchConditionTextService $searchConditionTextService, OutputBoundary $outputBoundary)
    {
        $this->dwhRafDao = $dwhRafDao;
        $this->rdbRafDao = $rdbRafDao;
        $this->productService = $productService;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->searchConditionTextService = $searchConditionTextService;
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

        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), $input->userId(), $input->baseDivision());
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
        $header = $this->searchConditionTextAppService->getRafCsv(...$csvParams);

        $this->dwhRafDao->commonCreateTempTables(...array_merge($params, [
            $input->axisType(),
            $input->channelAxis(),
            $input->period(),
            $input->dataTYpeFlags(),
            $input->axisTypeProduct(),
            $input->axisTypeCompany(),
        ]));

        $this->dwhRafDao->createCsvTempTable(...array_merge($params, [
            $input->axisType(),
            $input->channelAxis(),
            $input->period(),
            $input->dataTypeFlags(),
            $input->axisTypeProduct(),
            $input->axisTypeCompany(),
        ]));

        $data = (object) [
            'division' => $input->division(),
            'codes' => $input->codes(),
            'dataType' => $input->dataType(),
            'axisType' => $input->axisType(),
            'companyIds' => $companyIds,
            'productIds' => $input->productIds(),
            'regionId' => $input->regionId(),
            'channels' => $input->channels(),
            'channelAxis' => $input->channelAxis(),
            'period' => $input->period(),
            'codeList' => $codeList,
            'axisTypeCompany' => $input->axisTypeCompany(),
            'axisTypeProduct' => $input->axisTypeProduct(),
            'length' => 40000,
        ];

        $output = new OutputData($input->division(), $input->startDateShort(), $input->endDateShort(), $header, [$this, 'csvGenerator'], $data);

        ($this->outputBoundary)($output);
    }

    public function csvGenerator(stdClass $data): Traversable
    {
        $isConditionCross = $data->division === 'condition_cross';

        $verticalSummaryLabels = $this->createVerticalSummaryLabels();
        $verticalDetailLabels = $this->createVerticalDetailLabels();

        $codes = $data->codes;

        if ($data->division === 'condition_cross') {
            $codes = [
                'condition_cross',
                'household',
            ];
        }
        $codeHash = $this->getCodeHash($data->division, $data->codeList, $codes);
        $dataType = $this->searchConditionTextService->convertDataDivisionText($data->dataType);

        $next = 'code';

        if (!$isConditionCross && count($codes) === 1) {
            if ($data->axisType === $data->axisTypeCompany) {
                $next = 'company_id';
            }

            if ($data->axisType === $data->axisTypeProduct) {
                $next = 'product_id';
            }

            if ($data->channelAxis === '1') {
                $next = 'channel_id';
            }
        }

        $length = $data->length;
        $converted = [];
        $separate = [[], []];
        $riskAversionLimit = 1000; // 無限ループになる事はないが、念のため危機回避用の制限
        ini_set('memory_limit', '512M');

        for ($i = 0; $i < $riskAversionLimit; $i++) {
            $isEnd = false;
            $results = $this->dwhRafDao->selectTempResultsForCsv($length, $length * $i);

            if (count($results) === 0) {
                break;
            } elseif (count($results) < $length) {
                $isEnd = true;
            }
            $converted = $this->convertResultsForCsv($converted, $results, $data->companyIds, $data->productIds, $data->regionId, $data->channels, $data->axisType, $data->channelAxis, $data->period, $next, $data->axisTypeCompany, $data->axisTypeProduct);
            $results = null;
            $remainingData = [];

            foreach ($converted as $axis => $axisData) {
                foreach ($axisData as $channelAxis => $chAxisData) {
                    $hasSummary = true;
                    $verticalSummaryData = [];

                    foreach ($codes as $code) {
                        if (!isset($chAxisData['summary'][$code])) {
                            $hasSummary = false;
                            break;
                        }
                        $verticalSummaryData[] = $chAxisData['summary'][$code];
                    }

                    if ($hasSummary === false) {
                        $remainingData[$axis][$channelAxis] = $chAxisData;
                        continue;
                    }
                    $summaryData = $this->transverseMatrix(array_merge_recursive($verticalSummaryLabels, $verticalSummaryData));

                    yield $this->createSummaryCsv($chAxisData['headers'], $codes, $codeHash, $summaryData, $dataType);
                    yield $separate;

                    foreach ($codes as $code) {
                        $verticalDetailData = [];

                        foreach ($chAxisData['detail'][$code] as $key => $vertical) {
                            $verticalDetailData[] = $vertical;
                        }
                        $detailData = $this->transverseMatrix(array_merge_recursive($verticalDetailLabels, $verticalDetailData));
                        yield $this->createDetailCsv($chAxisData['headers'], $code, $codeHash, $detailData, $dataType);
                        yield $separate;
                    }
                }
            }
            $converted = $remainingData;

            if ($isEnd) {
                break;
            }
        }
    }

    private function createSummaryCsv(array $headers, array $codes, array $codeHash, array $summaryData, array $dataType): array
    {
        $csvData = [];
        $csvData[] = $headers['companies'];
        $csvData[] = $headers['products'];
        $headers['channels'][1] = $headers['channels'][1] . '_' . $dataType[1];
        $csvData[] = $headers['channels'];

        $codeLabel1 = ['', ''];
        $codeLabel2 = ['', ''];

        foreach ($codes as $code) {
            $codeLabel1[] = $codeHash[$code]['name'];
            $codeLabel2[] = $codeHash[$code]['description'];
        }
        $csvData[] = $codeLabel1;
        $csvData[] = $codeLabel2;

        return array_merge_recursive($csvData, $summaryData);
    }

    private function createDetailCsv(array $headers, string $code, array $codeHash, array $detailData, array $dataType): array
    {
        $csvData = [];
        $csvData[] = $headers['companies'];
        $csvData[] = $headers['products'];
        $headers['channels'][1] = $headers['channels'][1] . '_' . $dataType[1];
        $csvData[] = $headers['channels'];

        $csvData[] = ['集計区分：', $codeHash[$code]['name']];
        $csvData[] = ['集計条件：', $codeHash[$code]['description']];
        $csvData[] = ['集計期間内有効サンプル数：', $headers[$code]['number']];
        $csvData[] = [];

        return array_merge_recursive($csvData, $detailData);
    }

    private function getCodeHash(string $division, array $codeList, array $codes): array
    {
        $codeHash = [];

        if ($division !== 'condition_cross') {
            $codeNames = [];

            foreach ($codeList as $row) {
                $codeNames[$row->code] = $row->name;
            }
            $codeNames['personal'] = '個人計';
            $codeNames['household'] = '世帯';
            $codeNames['f'] = 'F';
            $codeNames['m'] = 'M';

            foreach ($codes as $code) {
                $codeHash[$code]['name'] = $codeNames[$code];
                $desc = null;

                switch ($code) {
                    case 'personal':
                        $desc = '４才以上男女';
                        break;
                    case 'household':
                        $desc = '';
                        break;
                    default:
                        if ($division === 'oc') {
                            $desc = $codeNames[$code];
                        }
                        break;
                }

                if (in_array($division, [
                        'ga8',
                        'ga10s',
                        'ga12',
                        'gm',
                    ]) && $desc === null) {
                    $desc = $this->descriptions[$division][$code];
                } elseif ($desc === null) {
                    $desc = '';
                }
                $codeHash[$code]['description'] = $desc;
            }
        } else {
            $codeHash['condition_cross']['name'] = '掛け合わせ条件';
            $codeHash['condition_cross']['description'] = '';
            $codeHash['household']['name'] = '世帯';
            $codeHash['household']['description'] = '';
        }
        return $codeHash;
    }

    private function convertResultsForCsv(?array $converted, array $results, ?array $companyIds, ?array $productIds, int $regionId, array $channels, string $axisType, ?string $channelAxis, string $period, string $next, string $axisTypeCompany, string $axisTypeProduct): array
    {
        if (empty($converted)) {
            $converted = [];
        }
        $products = [];
        $companies = [];
        $companyAxisNumber = $axisTypeCompany;
        $productAxisNumber = $axisTypeProduct;

        foreach ($results as $data) {
            $axis = 'all';
            $channel = 'all';

            if ($axisType === $companyAxisNumber) {
                $axis = $data->company_id;
            } elseif ($axisType === $productAxisNumber) {
                $axis = $data->product_id;
            }

            if ($channelAxis === '1') {
                $channel = $data->channel_id;
            }

            if (!isset($converted[$axis][$channel]['headers'])) {
                if ($axisType === $companyAxisNumber) {
                    if (isset($products[$data->company_id]) === false) {
                        $result = [];

                        if (!empty($productIds)) {
                            $list = $this->dwhRafDao->getProductNames($data->company_id, $productIds);

                            foreach ($list as $row) {
                                array_push($result, $row->name);
                            }
                        }
                        $products[$data->company_id] = implode('、', $result) ?: '指定なし';
                    }
                    $converted[$axis][$channel]['headers']['companies'] = ['企業名:', $data->company_name];
                    $converted[$axis][$channel]['headers']['products'] = ['商品名:', $products[$data->company_id]];
                } elseif ($axisType === $productAxisNumber) {
                    $converted[$axis][$channel]['headers']['companies'] = ['企業名:', $data->company_name];
                    $converted[$axis][$channel]['headers']['products'] = ['商品名:', $data->product_name];
                } else {
                    if (empty($companies)) {
                        $companies = $this->searchConditionTextService->convertCompanyNames($companyIds);
                    }

                    if (empty($products)) {
                        $products = $this->searchConditionTextService->convertProductNames($productIds);
                    }
                    $converted[$axis][$channel]['headers']['companies'] = $companies;
                    $converted[$axis][$channel]['headers']['products'] = $products;
                }

                if ($channelAxis === '1') {
                    $converted[$axis][$channel]['headers']['channels'] = ['放送:', $data->channel_name];
                } else {
                    $converted[$axis][$channel]['headers']['channels'] = $this->searchConditionTextService->convertChannels($channels, $regionId);
                }
            }
            $converted[$axis][$channel]['headers'][$data->code]['number'] = $data->rt_number;
            $verticalData = [];

            for ($i = 1; $i <= 6; $i++) {
                $var = "time_group_${i}_grp";
                $verticalData[] = $data->{$var};
            }
            $verticalData[] = $data->grp_summary;
            $verticalData[] = $data->reach_1;

            for ($i = 1; $i <= 10; $i++) {
                $var = "reach_${i}";
                $verticalData[] = $data->{$var};
            }
            $verticalData[] = $data->reach_avg;
            $verticalData[] = '';

            for ($i = 1; $i <= 20; $i++) {
                $var = "freq_${i}";
                $verticalData[] = $data->{$var};
            }

            if (!isset($converted[$axis][$channel]['detail'])) {
                $converted[$axis][$channel]['detail'] = [];
            }

            if ($this->isChangeAxis($next, $data)) {
                $summaryData = $verticalData;
                array_unshift($summaryData, $data->rt_number, '');
                $converted[$axis][$channel]['summary'][$data->code] = $summaryData;
            }

            $periodAxisData = $verticalData;
            $date = '';
            $c = new Carbon($data->date);

            if ($period === 'cm') {
                $hour = $c->hour;

                if ($hour < 5) {
                    $hour = $hour + 24;
                    $c->subDay();
                }
                $hour = sprintf('%02d', $hour);
                $date = $c->isoFormat('YYYY年MM月DD日（ddd）');
                $date .= $hour . $c->format('時i分s秒');
            } elseif ($period === 'day') {
                $date = $c->isoFormat('YYYY年MM月DD日（ddd）');
            } elseif ($period === 'week') {
                $date = $c->isoFormat('YYYY年MM月DD週');
            } elseif ($period === 'month') {
                $date = $c->isoFormat('YYYY年MM月');
            }
            array_unshift($periodAxisData, $date, '');

            $converted[$axis][$channel]['detail'][$data->code][] = $periodAxisData;
        }
        return $converted;
    }

    /**
     * @param string $axis
     * @param stdClass $data
     * @return bool
     * 集計軸の最終行かを判定する。
     * code 以外の場合は、 integer型 にて判定
     */
    private function isChangeAxis(string $axis, stdClass $data): bool
    {
        if ($axis === 'code') {
            return $data->{$axis} != $data->next;
        }
        return (int) $data->{$axis} != (int) $data->next;
    }

    private function createVerticalSummaryLabels(): array
    {
        $verticalSummaryLabels = [];
        $label1 = ['集計期間内有効サンプル数', '時間帯別 GRP　(%)'];
        $label2 = ['', ''];

        foreach ($this->timeGroupList as $value) {
            $label1[] = '';
            $label2[] = $value;
        }
        $label1[] = '延視聴率 GRP計　(%)';
        $label1[] = '累積到達率　Reach　(%)';
        $label2[] = '';
        $label2[] = '';

        for ($i = 1; $i <= 10; $i++) {
            $label1[] = '';
            $label2[] = "${i}回以上";
        }
        $label1[] = '平均視聴回数　(回)';
        $label1[] = '視聴の分布　frequency (%)';
        $label2[] = '';
        $label2[] = '';

        for ($i = 1; $i <= 20; $i++) {
            $label1[] = '';

            if ($i === 20) {
                $label2[] = "${i}回以上";
                continue;
            }
            $label2[] = "${i}回";
        }

        $verticalSummaryLabels[] = $label1;
        $verticalSummaryLabels[] = $label2;
        return $verticalSummaryLabels;
    }

    private function createVerticalDetailLabels(): array
    {
        $verticalDetailLabels = [];
        $label1 = ['', '時間帯別 GRP　(%)'];
        $label2 = ['検索期間TO', ''];

        foreach ($this->timeGroupList as $value) {
            $label1[] = '';
            $label2[] = $value;
        }
        $label1[] = '延視聴率 GRP計　(%)';
        $label1[] = '累積到達率　Reach　(%)';
        $label2[] = '';
        $label2[] = '';

        for ($i = 1; $i <= 10; $i++) {
            $label1[] = '';
            $label2[] = "${i}回以上";
        }
        $label1[] = '平均視聴回数　(回)';
        $label1[] = '視聴の分布　frequency (%)';
        $label2[] = '';
        $label2[] = '';

        for ($i = 1; $i <= 20; $i++) {
            $label1[] = '';

            if ($i === 20) {
                $label2[] = "${i}回以上";
                continue;
            }
            $label2[] = "${i}回";
        }

        $verticalDetailLabels[] = $label1;
        $verticalDetailLabels[] = $label2;
        return $verticalDetailLabels;
    }

    private function transverseMatrix(array $array): array
    {
        return call_user_func_array('array_map', array_merge([null], $array));
    }
}
