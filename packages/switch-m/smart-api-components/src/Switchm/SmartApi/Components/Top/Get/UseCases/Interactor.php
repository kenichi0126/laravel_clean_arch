<?php

namespace Switchm\SmartApi\Components\Top\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Queries\Dao\Rdb\TopDao;

class Interactor implements InputBoundary
{
    private $topDao;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param TopDao $topDao
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        TopDao $topDao,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->topDao = $topDao;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $regionId = $inputData->regionId();

        if ($regionId === 1) {
            $channelIds = [1, 3, 4, 5, 6, 7];
        } elseif ($regionId === 2) {
            $channelIds = [44, 46, 47, 48, 49, 50];
        }
        $live = $this->getLiveProgram($regionId, $channelIds, $inputData->channelColors(), $inputData->channelColorsKansai());

        $output = new OutputData($live['date'], $live['programs'], $live['chart'], $live['categories'], $live['phNumbers']);

        ($this->outputBoundary)($output);
    }

    /**
     * @param int $regionId
     * @param array $channelIds
     * @param array $channelColors
     * @param array $channelColorsKansai
     * @return array
     */
    private function getLiveProgram(int $regionId, array $channelIds, array $channelColors, array $channelColorsKansai): array
    {
        $termList = $this->topDao->getTerms($regionId);
        $terms = [];

        foreach ($termList as $row) {
            $terms[$row->name] = $row->datetime;
        }

        $targetDate = '';
        $programs = [];
        $datas = [];
        $categories = [];
        $timeReportsPrepared = new Carbon($terms['TimeReportsPrepared']);
        $liveTime = $timeReportsPrepared->toDateTimeString();

        $phNumbers = $this->searchConditionTextAppService->getPersonalHouseholdNumbers($timeReportsPrepared, $timeReportsPrepared, '', '', $regionId);

        foreach ($phNumbers as &$num) {
            $num = number_format($num);
        }

        if ($regionId === 1) {
            $programs =
                [
                    ['channel_id' => 1, 'code_name' => 'NHK'],
                    ['channel_id' => 3, 'code_name' => 'NTV'],
                    ['channel_id' => 4, 'code_name' => 'EX'],
                    ['channel_id' => 5, 'code_name' => 'TBS'],
                    ['channel_id' => 6, 'code_name' => 'TX'],
                    ['channel_id' => 7, 'code_name' => 'CX'],
                ];
        } else {
            $programs =
                [
                    ['channel_id' => 44, 'code_name' => 'NHKK'],
                    ['channel_id' => 46, 'code_name' => 'MBS'],
                    ['channel_id' => 47, 'code_name' => 'ABC'],
                    ['channel_id' => 48, 'code_name' => 'TVO'],
                    ['channel_id' => 49, 'code_name' => 'KTV'],
                    ['channel_id' => 50, 'code_name' => 'YTV'],
                ];
        }

        if (isset($terms['TimeReportsPrepared'])) {
            $hourViewingRates = $this->topDao->findHourViewingRate($liveTime, $regionId, $channelIds);
            $viewingRates = array_column(array_slice($hourViewingRates, 0, 6), 'viewing_rate', 'channel_id');

            if (count($hourViewingRates) > 0) {
                $targetDate = (new Carbon($hourViewingRates[0]['date']))->isoFormat('YYYY年MM月DD日（ddd）')
                    . '  ' . str_pad($hourViewingRates[0]['hour'], 2, '0', STR_PAD_LEFT)
                    . ':' . str_pad($hourViewingRates[0]['minute'], 2, '0', STR_PAD_LEFT);

                foreach ($programs as &$program) {
                    $program['viewing_rate'] = $viewingRates[$program['channel_id']];

                    if ($regionId === 1) {
                        $program['color'] = $channelColors[$program['channel_id']];
                    } elseif ($regionId === 2) {
                        $program['color'] = $channelColorsKansai[$program['channel_id']];
                    }
                }

                // ここからチャート用
                $categories = array_reverse($this->makeCategories($hourViewingRates[0]['minute']));
            } else {
                $programs = [];
            }

            $channels = array_keys($viewingRates);

            if (count($programs) > 0) {
                foreach ($channels as $channel) {
                    $data = [];
                    $channelName = array_filter($programs, function ($data) use ($channel) {
                        return $data['channel_id'] == $channel;
                    });
                    // reindex
                    $channelName = array_values($channelName);
                    $data['name'] = $channelName[0]['code_name'];

                    if ($regionId === 1) {
                        $data['color'] = $channelColors[$channel];
                    } elseif ($regionId === 2) {
                        $data['color'] = $channelColorsKansai[$channel];
                    }
                    $channelData = array_filter($hourViewingRates, function ($v) use ($channel) {
                        return $v['channel_id'] == $channel;
                    });

                    $data['data'] = [];

                    foreach ($channelData as $row) {
                        array_unshift($data['data'], ['x' => $row['datetime'], 'y' => $row['viewing_rate']]);
                    }
                    $datas[] = $data;
                }
            }
        }

        return [
            'date' => $targetDate,
            'programs' => $programs,
            'chart' => $datas,
            'categories' => $categories,
            'phNumbers' => $phNumbers,
        ];
    }

    /**
     * @param int $param
     * @return array
     */
    private function makeCategories(int $param): array
    {
        $results = [];

        for ($i = 0; $i <= 59; $i++) {
            $results[] = $param--;

            if ($param < 0) {
                $param = 59;
            }
        }
        return $results;
    }
}
