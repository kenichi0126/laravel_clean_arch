<?php

namespace Smart2\QueryModel\Service;

use Carbon\Carbon;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionTextDao;

class SearchConditionTextService
{
    private const COPYRIGHTS = ['データ提供元:', 'Switch Media Lab, Inc.'];

    /**
     * @var SearchConditionTextDao
     */
    private $searchConditionTextDao;

    /**
     * SearchConditionTextService constructor.
     * @param SearchConditionTextDao $searchConditionTextDao
     */
    public function __construct(SearchConditionTextDao $searchConditionTextDao)
    {
        $this->searchConditionTextDao = $searchConditionTextDao;
    }

    /**
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param null|string $code
     * @param string $dataDivision
     * @param null|string $displayType
     * @param null|string $aggregateType
     * @param array $conditionCross
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @param int $regionId
     * @param null|array $codeList
     * @param array $dateList
     * @param array $dataType
     * @param string $csvFlag
     * @return array
     */
    public function ratingPoints(string $startDateTime, string $endDateTime, string $channelType, array $channelIds, string $division, ?string $code, string $dataDivision, ?string $displayType, ?string $aggregateType, array $conditionCross, int $conditionCrossCount, int $tsConditionCrossCount, int $regionId, ?array $codeList, array $dateList, array $dataType, string $csvFlag)
    {
        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $isConditionCross = $division == 'condition_cross';

        // タイトル
        $title = [
            'SMART - 時間帯',
        ];

        // 期間
        $periodAverageStr = '';
        $dow = \Config::get('const.DOW');
        $sd = new Carbon($startDateTime);
        $ed = new Carbon($endDateTime);
        $startDate = $sd->format('Y年m月d日(' . $dow[$sd->dayOfWeek] . ')');
        $endDate = $ed->format('Y年m月d日(' . $dow[$ed->dayOfWeek] . ')');

        $weekFlag = $ed->weekOfYear !== $sd->weekOfYear;

        if ($weekFlag) {
            $periodAverageStr = '：期間平均';
        }
        $period = [
            '期間:',
            $startDate . '～' . $endDate . $periodAverageStr,
        ];

        // 放送
        switch ($channelType) {
            case 'advertising':
                if ($regionId === 1) {
                    $list = \Config::get('const.CHANNELS');
                    $channel = \Config::get('const.DIGITAL_FIVE');
                } elseif ($regionId === 2) {
                    $list = \Config::get('const.DIGITAL_KANSAI_CM');
                    $channel = \Config::get('const.DIGITAL_FIVE');
                }

                foreach ($channelIds as $val) {
                    $channels[] = $list[$val];
                }
                break;
            case 'dt1':
                $channel = '地デジ1';

                if ($displayType === 'dateBy') {
                    if ($regionId === 1) {
                        $channels = [
                            'NHK',
                            'NTV',
                            'EX',
                            'TBS',
                            'TX',
                            'CX',
                        ];
                    } elseif ($regionId === 2) {
                        $channels = [
                            'NHKK',
                            'MBS',
                            'ABC',
                            'TVO',
                            'KTV',
                            'YTV',
                        ];
                    }
                } else {
                    if ($regionId === 1) {
                        $channels = [
                            'NHK',
                            '日本テレビ',
                            'テレビ朝日',
                            'TBS',
                            'テレビ東京',
                            'フジテレビ',
                        ];
                    } elseif ($regionId === 2) {
                        $channels = [
                            'NHK関西',
                            '毎日放送',
                            '朝日放送',
                            'テレビ大阪',
                            '関西テレビ',
                            '讀賣テレビ',
                        ];
                    }
                }
                break;
            case 'dt2':
                $channel = '地デジ2';

                if ($regionId === 1) {
                    $channels = [
                        'ETV',
                        'UD',
                        'U局計',
                    ];
                } elseif ($regionId === 2) {
                    $channels = [
                        'ETVK',
                        'U局計',
                    ];
                }
                break;
            case 'bs1':
                $channel = 'BS1';
                $channels = [
                    'NHK BS1',
                    'NHK BSプレミアム',
                    'BS日テレ',
                    'BS朝日',
                ];
                break;
            case 'bs2':
                $channel = 'BS2';
                $channels = [
                    'BS-TBS',
                    'BSテレ東',
                    'BSフジ',
                    'WOWOWプライム',
                ];
                break;
            case 'bs3':
                $channel = 'BS3';
                $channels = [
                    'スター・チャンネル1',
                    'BS11',
                    'TwellV',
                ];
                break;
            case 'summary':
                $channel = '概要(放送別計)';
                $channels = [
                    '全局',
                    '地デジ',
                    'BS',
                ];
                break;
        }
        $channel_label = [
            '放送:',
            $channel,
        ];

        // カテゴリタイプ
        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        // サンプル条件
        $allCodes = array_column($codeList, 'code');
        $selected = $code;

        if ($isConditionCross) {
            $selected = true;
        }
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, [$code], $sd->format('Y-m-d'), $ed->format('Y-m-d'), $sd->format('Hi'), $ed->format('Hi'), $regionId, $csvFlag === '1', $dataType, $tsConditionCrossCount);

        if ($weekFlag) {
            $categoryHead['head'][] = '※期間内最新サンプル数';
            // TODO - fujisaki: 一時対応でサンプル数のデータ種別表記、SMART-Plus改修後修正
            if (in_array(\Config::get('const.DATA_TYPE_NUMBER.REALTIME'), $dataType)) {
                $categoryHead['head'][] = '（リアルタイム）';
            } else {
                $categoryHead['head'][] = '（タイムシフト）';
            }
            $categoryHead['head'][] = '';
        }

        if ($isConditionCross) {
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }

        $headers = $categoryHead['head'];

        // 表示種別
        $display = '';

        if ($displayType == 'dateBy') {
            $display = '日付・曜日別、';
        } else {
            $display = 'チャンネル別、';
        }

        // 集計種別
        if ($aggregateType == 'hourly') {
            $display .= '毎時';
        } else {
            $display .= $aggregateType . ':00' . '～' . $aggregateType . ':59';
        }
        $display = [
            '表示：',
            $display,
        ];

        // データ区分
        $dispDataDivision = '';

        switch ($dataDivision) {
            case 'viewing_rate':
                $dispDataDivision = '視聴率';
                break;
            case 'viewing_rate_share':
                $dispDataDivision = 'シェア';
                break;
            case 'target_content_personal':
                $dispDataDivision = 'ターゲット含有率（個人全体）';
                break;
            case 'target_content_household':
                $dispDataDivision = 'ターゲット含有率（世帯）';
                break;
        }
        $dataDivisionLabel = [
            'データ区分:',
            $dispDataDivision,
        ];

        // 単位
        $unitLabel = $this->convertUnit();

        // レポート作成日時
        $reportDateTimeLabel = $this->convertReportDateTime();

        $label = [];

        if ($displayType == 'dateBy') {
            $dateLabels = [];

            foreach ($dateList as $row) {
                $dateLabels[] = '';

                if ($weekFlag) {
                    $str = $dow[$row['carbon']->dayOfWeek];
                } else {
                    $str = $row['carbon']->format('n/j(' . $dow[$row['carbon']->dayOfWeek]);

                    if ($row['holidayFlg']) {
                        $str .= '・祝';
                    }
                    $str .= ')';
                }
                $dateLabels[] = $str;
                $dateLabels = array_merge($dateLabels, array_fill(1, count($channels) - 1, ''));
            }

            $channelLabels = [];

            foreach ($dateList as $row) {
                $channelLabels[] = '';

                foreach ($channels as $channel) {
                    $channelLabels[] = $channel;
                }
                $label = [
                    $dateLabels,
                    $channelLabels,
                ];
            }
        } else {
            $channelLabels = [];

            foreach ($channels as $channel) {
                $channelLabels[] = '';
                $channelLabels[] = $channel;
                $channelLabels = array_merge($channelLabels, array_fill(1, count($dateList) - 1, ''));
            }
            $dateLabels = [];
            $dowLabels = [];

            foreach ($channels as $channel) {
                $dateLabels[] = '';
                $dowLabels[] = '';

                foreach ($dateList as $row) {
                    $d = $dow[$row['carbon']->dayOfWeek];

                    if ($row['holidayFlg']) {
                        if (!$weekFlag) {
                            $d .= '・祝';
                        }
                    }
                    $dowLabels[] = $d;
                    $dateLabels[] = $row['carbon']->format('n月j日') . '(' . $d . ')';
                }

                if ($weekFlag) {
                    $label = [
                        $channelLabels,
                        $dowLabels,
                    ];
                } else {
                    $label = [
                        $channelLabels,
                        $dateLabels,
                    ];
                }
            }
        }

        $rows[] = $title;
        $rows[] = $period;
        $rows[] = $channel_label;
        $rows[] = $headers;
        $rows[] = $this->convertDataDivisionText($dataType);
        $rows[] = $dataDivisionLabel;
        $rows[] = $display;
        $rows[] = $unitLabel;
        $rows[] = $reportDateTimeLabel;
        $rows[] = self::COPYRIGHTS;
        $rows[] = [];

        $headers = array_merge($rows, $label);

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @param string $heatMapRating
     * @param string $heatMapTciPersonal
     * @param string $heatMapTciHousehold
     * @return array
     */
    public function getAdvertising(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg, string $heatMapRating, string $heatMapTciPersonal, string $heatMapTciHousehold): array
    {
        $adjust = 1;

        $cmMaterialFlag = \Auth()->user()->hasPermission('smart2::cm_materials::view');
        $cmTypeFlag = \Auth()->user()->hasPermission('smart2::time_spot::view');

        $headers = [];

        $headers[] = [
            'SMART  -  CM出稿数',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        // オプション
        $headers[] = ['視聴率表示:', $heatMapRating === 'true' || $heatMapRating === '1' ? 'ON' : 'OFF'];

        // オプション 2
        // TODO - fujisaki: UIから送られるデータの型を統一する、もしくはcsvも含めて全てpostで送りboolはboolのまま送るようにする必要がある（対応タイミングは未定）
        $heatMapTciPersonal = $heatMapTciPersonal === 'true' || $heatMapTciPersonal === '1';
        $heatMapTciHousehold = $heatMapTciHousehold === 'true' || $heatMapTciHousehold === '1';

        if ($heatMapTciPersonal === true || $heatMapTciHousehold === true) {
            $headers[] = ['含有率表示:', 'ON'];
        } else {
            $headers[] = ['含有率表示:', 'OFF'];
        }

        // 企業
        $headers[] = $this->convertCompanyNames($companyIds);

        // 商品
        $headers[] = $this->convertProductNames($productIds);

        // 素材別
        if ($cmMaterialFlag) {
            $headers[] = $this->convertCmMaterials($cmIds);
        } else {
            $adjust++;
        }

        // 広告種別（権限から取得）
        if ($cmTypeFlag) {
            $headers[] = $this->convertCmTypeFlag($cmType);
        } else {
            $adjust++;
        }

        // CM秒数（リクエストから取得し文字列化）
        $headers[] = $this->convertCmSeconds($cmSeconds);
        // 単位
        $headers[] = $this->convertUnit('本');
        // 現在日時
        $headers[] = $this->convertReportDateTime();
        // コピーライト
        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        $labelChannel = [''];
        $labelDow = [];

        if ($regionId === 1) {
            $channelList = \Config::get('const.CHANNELS');
        } elseif ($regionId === 2) {
            $channelList = \Config::get('const.DIGITAL_KANSAI_CM');
        }

        foreach ($channels as $channel) {
            $labelChannel[] = $channelList[$channel];
            $labelChannel = array_merge($labelChannel, [
                '',
                '',
                '',
                '',
                '',
                '',
            ]); // 火～日

            $labelDow[] = '';

            foreach (\Config::get('const.DOW') as $dow) {
                $labelDow[] = $dow;
            }
            $labelChannel[] = '';
        }

        $headers[] = $labelChannel;
        $headers[] = $labelDow;

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|array $wdays
     * @param bool $isHoliday
     * @param null|array $channels
     * @param null|array $genres
     * @param null|array $progIds
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param null $order
     * @param null $length
     * @param int $regionId
     * @param null $page
     * @param $straddlingFlg
     * @param bool $bsFlg
     * @param string $csvFlag
     * @param bool $programListExtensionFlag
     * @param array $dataType
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @param string $digitalAndBs
     * @param array $codeList
     * @return array
     */
    public function getProgramList(string $startDate, string $endDate, string $startTime, string $endTime, ?array $wdays, bool $isHoliday, ?array $channels, ?array $genres, ?array $progIds, string $division, ?array $conditionCross, ?array $codes, $order, $length, int $regionId, $page, $straddlingFlg, bool $bsFlg, string $csvFlag, bool $programListExtensionFlag, array $dataType, int $conditionCrossCount, int $tsConditionCrossCount, string $digitalAndBs, array $codeList): array
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($dataType);

        $adjust = 1;

        $isConditionCross = $division == 'condition_cross';

        if ($isConditionCross) {
            $codes = [];
        }

        $headers = [];

        $headers[] = [
            'SMART - 番組リスト',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime, false);

        // 放送（リクエストから取得）
        $headers[] = $this->convertProgramListChannels($channels, $digitalAndBs, $regionId);

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag === '1', $dataType, $tsConditionCrossCount);
        $categoryHead['head'][] = '※期間内最新サンプル数';

        $dataTypeLabels = [];

        if ($isRt) {
            $dataTypeLabels[] = 'リアルタイム';
        }

        if ($isTs || $isGross || $isRtTotal) {
            $dataTypeLabels[] = 'タイムシフト';
        }
        $categoryHead['head'][] = '（' . implode('/', $dataTypeLabels) . '）';

        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        // 時間帯
        $headers[] = $this->convertTimePeriod($startTime, $endTime);

        // 曜日
        $headers[] = $this->convertDow($wdays, $isHoliday);

        // ジャンル
        $headers[] = $this->convertGenres($genres);

        // 番組名
        $headers[] = $this->convertProgramNames($progIds);

        if ($csvFlag === '1' && !$bsFlg && $programListExtensionFlag) {
            $headers[] = [
                '番組拡張オプション:',
                '有効',
            ];
        } else {
            $adjust++;
        }

        // コピーライト
        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        $selectedCategories = [];
        $selectedCategoriesEnd = [];
        $selectedCategoriesTime = [];
        $selectedCategoriesPt = [];
        $selectedCategoriesSb = [];

        foreach ($dataType as $type) {
            if (in_array('personal', $codes) && !$isConditionCross) {
                $selectedCategories[] = '個人全体' . ($programListExtensionFlag ? '(平均)_' . $this->convertDataTypeText($type) : '_' . $this->convertDataTypeText($type));

                if (\Config::get('const.DATA_TYPE_NUMBER.REALTIME') == $type) {
                    $selectedCategoriesEnd[] = '個人全体(終了時)';
                    $selectedCategoriesTime[] = '個人全体(TIME平均)';
                    $selectedCategoriesPt[] = '個人全体(PT平均)';
                    $selectedCategoriesSb[] = '個人全体(SB平均)';
                }
            }

            if ($isConditionCross) {
                $selectedCategories[] = '掛け合わせ条件' . ($programListExtensionFlag ? '(平均)_' . $this->convertDataTypeText($type) : '_' . $this->convertDataTypeText($type));

                if (\Config::get('const.DATA_TYPE_NUMBER.REALTIME') == $type) {
                    $selectedCategoriesEnd[] = '掛け合わせ条件(終了時)';
                    $selectedCategoriesTime[] = '掛け合わせ条件(TIME平均)';
                    $selectedCategoriesPt[] = '掛け合わせ条件(PT平均)';
                    $selectedCategoriesSb[] = '掛け合わせ条件(SB平均)';
                }
            }

            foreach ($codes as $code) {
                foreach ($categoryHead['numbers'] as $row) {
                    if ($row->code == $code) {
                        $selectedCategories[] = $row->name . ($programListExtensionFlag ? '(平均)_' . $this->convertDataTypeText($type) : '_' . $this->convertDataTypeText($type));

                        if (\Config::get('const.DATA_TYPE_NUMBER.REALTIME') == $type) {
                            $selectedCategoriesEnd[] = $row->name . '(終了時)';
                            $selectedCategoriesTime[] = $row->name . '(TIME平均)';
                            $selectedCategoriesPt[] = $row->name . '(PT平均)';
                            $selectedCategoriesSb[] = $row->name . '(SB平均)';
                        }
                    }
                }
            }

            if (in_array('household', $codes) || $isConditionCross) {
                $selectedCategories[] = '世帯(平均)' . '_' . $this->convertDataTypeText($type);

                if (\Config::get('const.DATA_TYPE_NUMBER.REALTIME') == $type) {
                    $selectedCategoriesEnd[] = '世帯(終了時)';
                    $selectedCategoriesTime[] = '世帯(TIME平均)';
                    $selectedCategoriesPt[] = '世帯(PT平均)';
                    $selectedCategoriesSb[] = '世帯(SB平均)';
                }
            }
        }

        // 番組リスト拡張オプションフラグがONの場合は、各属性の終了時、TIME平均、PT平均、SB平均を出力する
        if (!$bsFlg) {
            if ($programListExtensionFlag) {
                $selectedCategories = array_merge($selectedCategories, $selectedCategoriesEnd);
                $selectedCategories = array_merge($selectedCategories, $selectedCategoriesTime);
                $selectedCategories = array_merge($selectedCategories, $selectedCategoriesPt);
                $selectedCategories = array_merge($selectedCategories, $selectedCategoriesSb);
            } else {
                if (in_array('household', $codes) || $isConditionCross) {
                    $selectedCategories = array_merge($selectedCategories, [
                        '世帯(終了時)',
                    ]);
                }
            }

            if (in_array('household', $codes) || $isConditionCross) {
                $selectedCategories = array_merge($selectedCategories, [
                    '世帯(占拠率)',
                ]);
            }
        }

        $labels = [
            '放送日',
            '曜日',
            '放送開始',
            '放送終了',
            '放送分数',
            '放送局',
            'ジャンル',
            '番組名',
        ];

        // カラム数対応
        $selectedCategories = $this->addPostFixCategory($selectedCategories, 1);

        $labels = array_merge($labels, $selectedCategories);

        $headers[] = $labels;

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param int $regionId
     * @param int $conditionCrossCount
     * @param array $codeList
     * @param int $hour
     * @return array
     */
    public function getProgramTable(string $startDate, string $endDate, string $startTime, string $endTime, string $division, ?array $conditionCross, ?array $codes, int $regionId, int $conditionCrossCount, array $codeList, int $hour): array
    {
        $isConditionCross = $division == 'condition_cross';

        if ($isConditionCross) {
            $codes = [];
        }

        $headers = [];

        $headers[] = [
            '番組表',
        ];

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId);
        // サンプル条件
        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        // 表示
        $headers[] = [
            "表示:${hour}時間表示、番組平均視聴データ(番組終了時)",
        ];

        // レポート作成日時
        $headers[] = $this->convertReportDateTime();

        // 単位
        $headers[] = $this->convertUnit();

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param array $wdays
     * @param bool $isHoliday
     * @param array $channels
     * @param null|array $genres
     * @param array $programTypes
     * @param int $regionId
     * @param string $division
     * @param array $conditionCross
     * @param null|array $codes
     * @param string $dispAverage
     * @param null|array $codeList
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @param array $dataType
     * @param string $csvFlag
     * @return array
     */
    public function getPeriodAverage(string $startDate, string $endDate, string $startTime, string $endTime, array $wdays, bool $isHoliday, array $channels, ?array $genres, array $programTypes, int $regionId, string $division, array $conditionCross, ?array $codes, string $dispAverage, ?array $codeList, int $conditionCrossCount, int $tsConditionCrossCount, array $dataType, string $csvFlag): array
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($dataType);

        $isConditionCross = $division == 'condition_cross';

        if ($isConditionCross) {
            $codes = [];
        }

        $headers = [];

        $headers[] = [
            'SMART - 番組期間平均',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime, false);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag === '1', $dataType, $tsConditionCrossCount);
        $categoryHead['head'][] = '※期間内最新サンプル数';

        $dataTypeLabels = [];

        if ($isRt) {
            $dataTypeLabels[] = 'リアルタイム';
        }

        if ($isTs || $isGross || $isRtTotal) {
            $dataTypeLabels[] = 'タイムシフト';
        }
        $categoryHead['head'][] = '（' . implode('/', $dataTypeLabels) . '）';

        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        // 時間帯
        $headers[] = $this->convertTimePeriod($startTime, $endTime);

        // 曜日
        $headers[] = $this->convertDow($wdays, $isHoliday);

        // ジャンル
        $headers[] = $this->convertGenres($genres);

        // 番組タイプ
        $headers[] = $this->convertProgType($programTypes);

        // 表示
        $headers[] = [
            '表示',
            $dispAverage == 'weight' ? '加重平均' : '単純平均',
        ];

        // 単位
        $headers[] = $this->convertUnit();

        // レポート作成日時
        $headers[] = $this->convertReportDateTime();

        // コピーライト
        $headers[] = self::COPYRIGHTS;

        $headers[] = [];

        $selectedCategories = [];

        if (in_array('personal', $codes)) {
            $selectedCategories[] = '個人全体';
        }

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($codes as $code) {
            foreach ($categoryHead['numbers'] as $row) {
                if ($row->code == $code) {
                    $selectedCategories[] = $row->name;
                    break;
                }
            }
        }

        if ($isConditionCross) {
            $selectedCategories[] = '掛け合わせ';
        }

        if (in_array('household', $codes) || $isConditionCross) {
            $selectedCategories[] = '世帯';
        }

        $labels = [
            '通常開始時刻',
            '通常終了時刻',
            '曜日',
            '放送局',
            '番組名',
            '番組タイプ',
            '本数',
            '通常分数',
            '分数計',
        ];

        // カラム数対応
        $resultCategories = $this->convertCategories($selectedCategories, $dataType);
        $labels = array_merge($labels, $resultCategories);

        $headers[] = $labels;

        return $headers;
    }

    /**
     * @param bool $isEnq
     * @param int $regionId
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param array $channelIds
     * @param array $selectedPrograms
     * @param string $sampleType
     * @param array $personalAndHouseholdResults
     * @return array
     */
    public function getMultiChannelProfile(bool $isEnq, int $regionId, string $startDate, string $endDate, string $startTime, string $endTime, array $channelIds, array $selectedPrograms, string $sampleType, array $personalAndHouseholdResults): array
    {
        $headers = [];

        $headers[] = [
            'SMART - 番組ターゲットインデックス',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime, false);

        $dataType = [\Config::get('const.DATA_TYPE_NUMBER.REALTIME')];

        // サンプル
        $categoryHead = $this->convertCategoryHead(0, 'ga8', ['personal', 'household'], $startDate, $endDate, $startTime, $endTime, $regionId, true);
        $categoryHead['head'][] = '※期間内最新サンプル数';
        $categoryHead['head'][] = '（リアルタイム）';
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        $headers[] = $this->convertProfileMainChannel($channelIds[0], $regionId);

        $selectedProgramsHeader = ['番組名:'];

        foreach ($selectedPrograms as $program) {
            $selectedProgramsHeader[] = $program->title . ' | ' . $program->real_started_at_list;
        }
        $headers[] = $selectedProgramsHeader;

        $sampleTypeNames = \Config::get('const.SAMPLE_TYPE_NAME');
        $headers[] = $sampleTypeHeader = ['サンプル区分:', $sampleTypeNames[$sampleType]];

        // レポート作成日時
        $headers[] = $this->convertReportDateTime();

        // コピーライト
        $headers[] = self::COPYRIGHTS;

        $headers[] = [];
        $headers[] = [];
        $headers[] = [];
        $headers[] = [];

        $channelCodeNames = $this->getChannelCodeNames($channelIds);
        $personalAndHouseholdLabels = [''];
        $trpLabels = [];
        $tciLabels = [];
        $isFirst = true;

        foreach ($channelIds as $id) {
            if ($isFirst) {
                $trpLabels[] = $channelCodeNames[$id] . '順位_視聴率';
                $tciLabels[] = $channelCodeNames[$id] . '順位_ターゲット含有率';
                $isFirst = false;
            }
            $trpLabels[] = $channelCodeNames[$id] . '視聴率';
            $tciLabels[] = $channelCodeNames[$id] . 'ターゲット含有率';
            $personalAndHouseholdLabels[] = $channelCodeNames[$id] . '視聴率';
        }

        $headers[] = $personalAndHouseholdLabels;

        foreach ($personalAndHouseholdResults as $result) {
            $headers[] = json_decode(json_encode($result), true);
        }

        $headers[] = [];
        $headers[] = [];

        if ($isEnq) {
            $labels = [
                'アンケートID',
                '商品ジャンル',
                'アンケート項目',
                '選択肢',
                'サンプル数',
            ];
        } else {
            $labels = [
                '',
                '',
                'サンプル区分',
                'サンプル名',
                'サンプル数',
            ];
        }

        $headers[] = array_merge($labels, $trpLabels, $tciLabels);

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|array $order
     * @param null|int $page
     * @param null|int $length
     * @param null|bool $conv15SecFlag
     * @param bool $straddlingFlg
     * @param null|array $codeList
     * @param bool $csvFlag
     * @param array $dataType
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @return array
     */
    public function getList(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, ?int $page, ?int $length, ?bool $conv15SecFlag, bool $straddlingFlg, ?array $codeList, bool $csvFlag, array $dataType, int $conditionCrossCount, int $tsConditionCrossCount): array
    {
        $adjust = 1;

        $isConditionCross = false;

        if ($division === 'condition_cross') {
            $isConditionCross = true;
            $codes = [];
        }

        $cmMaterialFlag = \Auth()->user()->hasPermission('smart2::cm_materials::view');
        $cmTypeFlag = \Auth()->user()->hasPermission('smart2::time_spot::view');

        $headers = [];

        $headers[] = [
            'SMART  -  CMリスト',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag, $dataType, $tsConditionCrossCount);

        $categoryHead['head'][] = '※期間内最新サンプル数';
        // TODO - fujisaki: 一時対応でサンプル数のデータ種別表記、SMART-Plus改修後修正
        if (in_array(\Config::get('const.DATA_TYPE_NUMBER.REALTIME'), $dataType)) {
            $categoryHead['head'][] = '（リアルタイム）';
        } else {
            $categoryHead['head'][] = '（タイムシフト）';
        }
        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        // 企業
        $headers[] = $this->convertCompanyNames($companyIds);

        // 商品
        $headers[] = $this->convertProductNames($productIds);

        // 素材別
        if ($cmMaterialFlag) {
            $headers[] = $this->convertCmMaterials($cmIds);
        } else {
            $adjust++;
        }

        // 広告種別（権限から取得）
        if ($cmTypeFlag) {
            $headers[] = $this->convertCmTypeFlag($cmType);
        } else {
            $adjust++;
        }

        // CM秒数（リクエストから取得し文字列化）
        $headers[] = $this->convertCmSeconds($cmSeconds);
        // 番組
        $headers[] = $this->convertProgramNames($progIds);

        $headers[] = [
            '測定点:', 'CM開始以前の0/15/30/45秒のうち、最も近い点を測定点とする',
        ];

        // 15秒変換フラグ（リクエストから取得予定）
        $headers[] = $this->convert15SecFlag($conv15SecFlag);

        // 単位
        $headers[] = $this->convertUnit();

        // 現在日時
        $headers[] = $this->convertReportDateTime();

        // コピーライト
        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        $selectedCategories = [];

        foreach (\Config::get('const.DATA_TYPE_NUMBER') as $type) {
            if (!in_array($type, $dataType)) {
                continue;
            }

            if (in_array('personal', $codes)) {
                $selectedCategories[] = '個人全体_' . $this->convertDataTypeText($type);
            }

            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
            $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

            if (in_array($division, \Config::get('const.BASE_DIVISION')) && $dispSelectedPersonal && !$isConditionCross) {
                $selectedCategories[] = '個人選択計_' . $this->convertDataTypeText($type);
            }

            foreach ($codes as $code) {
                foreach ($categoryHead['numbers'] as $row) {
                    if ($row->code == $code) {
                        $selectedCategories[] = $row->name . '_' . $this->convertDataTypeText($type);
                        break;
                    }
                }
            }

            if ($isConditionCross) {
                $selectedCategories[] = '掛け合わせ_' . $this->convertDataTypeText($type);
            }

            if (in_array('household', $codes) || $isConditionCross) {
                $selectedCategories[] = '世帯_' . $this->convertDataTypeText($type);
            }
        }

        // カラム数対応
        $selectedCategories = $this->addPostFixCategory($selectedCategories, 1);

        $labels = [
            '日付',
            '曜日',
            '開始時刻',
            '秒数',
        ];

        if ($cmTypeFlag) {
            $labels[] = 'CM種別';
        }
        $labels[] = '企業';
        $labels[] = '商品';

        if ($cmMaterialFlag) {
            $labels[] = 'CMID';
            $labels[] = '状況設定';
            $labels[] = '出演者';
            $labels[] = 'BGM';
            $labels[] = 'メモ欄';
        }
        $labels = array_merge($labels, $selectedCategories);
        $labels[] = '放送局';
        $labels[] = '番組';

        $headers[] = $labels;

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv15SecFlag
     * @param string $period
     * @param null|string $allChannels
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param array $dataType
     * @param null|array $codeList
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @return array
     */
    public function getGrp(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, ?array $codeList, int $conditionCrossCount, int $tsConditionCrossCount): array
    {
        $adjust = 1;

        $isConditionCross = false;

        if ($division === 'condition_cross') {
            $isConditionCross = true;
            $codes = [];
        }

        $cmMaterialFlag = \Auth()->user()->hasPermission('smart2::cm_materials::view');
        $cmTypeFlag = \Auth()->user()->hasPermission('smart2::time_spot::view');

        $headers = [];

        $headers[] = [
            'SMART  -  CM: GRP計',
        ];
        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime);

        // 期間区切り
        $headers[] = $this->convertPeriodDivision($period);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag === '1', $dataType, $tsConditionCrossCount);

        $categoryHead['head'][] = '※期間内最新サンプル数';
        // TODO - fujisaki: 一時対応でサンプル数のデータ種別表記、SMART-Plus改修後修正
        if (in_array(\Config::get('const.DATA_TYPE_NUMBER.REALTIME'), $dataType)) {
            $categoryHead['head'][] = '（リアルタイム）';
        } else {
            $categoryHead['head'][] = '（タイムシフト）';
        }
        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        // 企業
        $headers[] = $this->convertCompanyNames($companyIds);

        // 商品
        $headers[] = $this->convertProductNames($productIds);

        // 素材別
        if ($cmMaterialFlag) {
            $headers[] = $this->convertCmMaterials($cmIds);
        } else {
            $adjust++;
        }

        // 広告種別（権限から取得）
        if ($cmTypeFlag) {
            $headers[] = $this->convertCmTypeFlag($cmType);
        } else {
            $adjust++;
        }

        // CM秒数（リクエストから取得し文字列化）
        $headers[] = $this->convertCmSeconds($cmSeconds);
        // 番組
        $headers[] = $this->convertProgramNames($progIds);

        // 15秒変換フラグ（リクエストから取得予定）
        $headers[] = $this->convert15SecFlag($conv15SecFlag);

        // 単位
        $headers[] = $this->convertUnit();

        // 全局表示
        $headers[] = $this->convertAllChanelsText($allChannels);

        // 現在日時
        $headers[] = $this->convertReportDateTime();

        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        $selectedCategories = [];

        foreach (\Config::get('const.DATA_TYPE_NUMBER') as $type) {
            if (!in_array($type, $dataType)) {
                continue;
            }

            if (in_array('personal', $codes)) {
                $selectedCategories[] = '個人全体_' . $this->convertDataTypeText($type);
            }

            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
            $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

            if (in_array($division, \Config::get('const.BASE_DIVISION')) && $dispSelectedPersonal && !$isConditionCross) {
                $selectedCategories[] = '個人選択計_' . $this->convertDataTypeText($type);
            }

            foreach ($codes as $code) {
                foreach ($categoryHead['numbers'] as $row) {
                    if ($row->code == $code) {
                        $selectedCategories[] = $row->name . '_' . $this->convertDataTypeText($type);
                        break;
                    }
                }
            }

            if ($isConditionCross) {
                $selectedCategories[] = '掛け合わせ_' . $this->convertDataTypeText($type);
            }

            if (in_array('household', $codes) || $isConditionCross) {
                $selectedCategories[] = '世帯_' . $this->convertDataTypeText($type);
            }
        }

        // カラム数対応
        $selectedCategories = $this->addPostFixCategory($selectedCategories, 1);

        $labels[] = '期間';
        $labels[] = '企業';
        $labels[] = '商品';
        $labels[] = '本数';
        $labels[] = '秒数';
        // TODO: selectedCategories のタイムシフト対応
        $labels = array_merge($labels, $selectedCategories);

        $headers[] = $labels;

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|string $cmType
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param array $channels
     * @param null|array $order
     * @param null|bool $conv15SecFlag
     * @param string $period
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param array $dataType
     * @param null|array $cmLargeGenres
     * @param string $axisType
     * @param null|array $codeList
     * @param int $conditionCrossCount
     * @return array
     */
    public function getRankingCommercial(String $startDate, String $endDate, String $startTime, String $endTime, ?array $wdays, bool $holiday, ?String $cmType, int $regionId, String $division, ?array $codes, ?array $conditionCross, array $channels, ?array $order, ?bool $conv15SecFlag, string $period, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, ?array $cmLargeGenres, string $axisType, ?array $codeList, int $conditionCrossCount): array
    {
        $adjust = 1;

        $isConditionCross = false;

        if ($division === 'condition_cross') {
            $isConditionCross = true;
            $codes = [];
        }

        $cmMaterialFlag = \Auth()->user()->hasPermission('smart2::cm_materials::view');
        $cmTypeFlag = \Auth()->user()->hasPermission('smart2::time_spot::view');

        $headers = [];

        $headers[] = [
            'SMART  -  CMランキング',
        ];
        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime);

        // 集計軸
        $headers[] = $this->convertAxisTypeText($axisType);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag === '1');

        $categoryHead['head'][] = '※期間内最新サンプル数';
        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        // 曜日
        $headers[] = $this->convertDow($wdays, $holiday);

        // CMジャンル大分類名取得
        $headers[] = $this->convertCmLargeGenres($cmLargeGenres);

        // 広告種別（権限から取得）
        if ($cmTypeFlag) {
            $headers[] = $this->convertCmTypeFlag($cmType);
        } else {
            $adjust++;
        }

        // 15秒変換フラグ（リクエストから取得予定）
        $headers[] = $this->convert15SecFlag($conv15SecFlag);

        // 単位
        $headers[] = $this->convertUnit();

        // 現在日時
        $headers[] = $this->convertReportDateTime();

        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        $selectedCategories = [];

        if (in_array('personal', $codes)) {
            $selectedCategories[] = '個人全体';
        }

        foreach ($codes as $code) {
            foreach ($categoryHead['numbers'] as $row) {
                if ($row->code == $code) {
                    $selectedCategories[] = $row->name;
                    break;
                }
            }
        }

        if ($isConditionCross) {
            $selectedCategories[] = '掛け合わせ';
        }

        if (in_array('household', $codes) || $isConditionCross) {
            $selectedCategories[] = '世帯';
        }
        // カラム数対応
        $selectedCategories = $this->addPostFixCategory($selectedCategories, 1);
        $labels[] = '順位';
        $labels[] = '企業';

        if ($axisType == \Config::get('const.AXIS_TYPE_NUMBER.PRODUCT')) {
            $labels[] = '商品';
            $labels[] = 'ジャンル';
        }
        $labels[] = '本数';
        $labels[] = '秒数';
        // TODO: selectedCategories のタイムシフト対応
        $labels = array_merge($labels, $selectedCategories);
        $headers[] = $labels;

        return $headers;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codeList
     * @param int $conditionCrossCount
     * @param int $tsConditionCrossCount
     * @param string $conv15SecFlag
     * @param null|array $codes
     * @param array $dataType
     * @param string $csvFlag
     * @param string $period
     * @return array
     */
    public function getRaf(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg, string $division, ?array $conditionCross, ?array $codeList, int $conditionCrossCount, int $tsConditionCrossCount, string $conv15SecFlag, ?array $codes, array $dataType, string $csvFlag, string $period): array
    {
        $this->searchConditionTextDao = new SearchConditionTextDao();

        $isConditionCross = false;

        if ($division === 'condition_cross') {
            $isConditionCross = true;
            $codes = [];
        }

        $adjust = 1;

        $cmMaterialFlag = \Auth()->user()->hasPermission('smart2::cm_materials::view');
        $cmTypeFlag = \Auth()->user()->hasPermission('smart2::time_spot::view');

        $headers = [];

        $headers[] = [
            'SMART  -  CM: リーチ＆フリークエンシー',
        ];

        // 検索期間（リクエストから取得）
        $headers[] = $this->convertPeriod($startDate, $endDate, $startTime, $endTime);

        if ($csvFlag === '1') {
            // 期間区切り
            $headers[] = $this->convertPeriodDivision($period);
        }

        // 個人数・選択したアスペクトのカテゴリ別人数・世帯数を取得
        $categoryHead = $this->convertCategoryHead($conditionCrossCount, $division, $codes, $startDate, $endDate, $startTime, $endTime, $regionId, $csvFlag === '1', $dataType, $tsConditionCrossCount);
        $categoryHead['head'][] = '※期間内最新サンプル数';
        // TODO - fujisaki: 一時対応でサンプル数のデータ種別表記、SMART-Plus改修後修正
        if (in_array(\Config::get('const.DATA_TYPE_NUMBER.REALTIME'), $dataType)) {
            $categoryHead['head'][] = '（リアルタイム）';
        } else {
            $categoryHead['head'][] = '（タイムシフト）';
        }
        // かけ合わせ条件（リクエストから取得し文字列化）
        if ($isConditionCross) {
            $categoryHead['head'][] = '';
            $categoryHead['head'] = array_merge($categoryHead['head'], $this->convertCrossConditionText($conditionCross));
        }
        $headers[] = $categoryHead['head'];

        $headers[] = $this->convertDataDivisionText($dataType);

        // 企業
        $headers[] = $this->convertCompanyNames($companyIds);

        // 商品
        $headers[] = $this->convertProductNames($productIds);

        // 素材別
        if ($cmMaterialFlag) {
            $headers[] = $this->convertCmMaterials($cmIds);
        } else {
            $adjust++;
        }

        // 広告種別（権限から取得）
        if ($cmTypeFlag) {
            $headers[] = $this->convertCmTypeFlag($cmType);
        } else {
            $adjust++;
        }

        // CM秒数（リクエストから取得し文字列化）
        $headers[] = $this->convertCmSeconds($cmSeconds);

        // 番組名
        $headers[] = $this->convertProgramNames($progIds);

        // 放送（リクエストから取得）
        $headers[] = $this->convertChannels($channels, $regionId);

        // 15秒換算
        $headers[] = $this->convert15SecFlag($conv15SecFlag);

        // 単位
        $headers[] = $this->convertUnit();

        // 現在日時
        $headers[] = $this->convertReportDateTime();

        $headers[] = ['※注意　　本レポートのGRPは、集計期間内有効サンプルを母数に算出した集計値です。'];
        // コピーライト
        $headers[] = self::COPYRIGHTS;

        for ($i = 0; $i < $adjust; $i++) {
            $headers[] = [];
        }

        return $headers;
    }

    /**
     * @param string $division
     * @param array $conditionCross
     * @return null|string
     */
    public function getConvertedCrossConditionText(string $division, array $conditionCross): string
    {
        if ($division === 'condition_cross') {
            return $this->convertCrossConditionText($conditionCross)[1];
        }
        return '';
    }

    public function convertCompanyNames(?array $companyIds): array
    {
        $companies = $this->getCompanyNames($companyIds) ?: '指定なし';
        $companyNames = [
            '企業名:',
            $companies,
        ];

        return $companyNames;
    }

    public function convertProductNames(?array $productIds): array
    {
        $products = $this->getProductNames($productIds) ?: '指定なし';
        $productNames = [
            '商品名:',
            $products,
        ];
        return $productNames;
    }

    public function convertChannels(array $channels, int $regionId): array
    {
        if ($regionId === 1) {
            $channelList = \Config::get('const.CHANNELS');
            $allString = \Config::get('const.DIGITAL_FIVE');
        } elseif ($regionId === 2) {
            $channelList = \Config::get('const.DIGITAL_KANSAI_CM');
            $allString = \Config::get('const.DIGITAL_FIVE');
        }

        if (count($channelList) !== count($channels)) {
            $arr = [];

            foreach ($channels as $id) {
                $arr[] = $channelList[$id];
            }
            $dispChannels = implode('、', $arr);
        } else {
            $dispChannels = $allString;
        }
        $channelHead = [
            '放送:',
            $dispChannels,
        ];

        return $channelHead;
    }

    public function convertDataDivisionText(?array $dataType)
    {
        $dataTypes = \Config::get('const.DATA_TYPE');
        $resultArray = [];

        foreach ($dataType as $type) {
            array_push($resultArray, $dataTypes[$type]);
        }
        return ['データ種別:', implode('/', $resultArray)];
    }

    public function getPersonalHouseholdNumbers(string $startDate, string $endDate, string $startTime, string $endTime, int $regionId, bool $isRt = true): array
    {
        $list = $this->searchConditionTextDao->getPersonalHouseholdNumbers($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $regionId, $isRt);
        $result = [];

        foreach ($list as $row) {
            $result[$row->division] = $row->number;
        }

        if (empty($list)) {
            $result['personal'] = 0;
            $result['household'] = 0;
        }
        return $result;
    }

    protected function convertDataTypeText(?int $type)
    {
        $dataTypes = \Config::get('const.DATA_TYPE_HEADER');
        return $dataTypes[$type];
    }

    protected function convertCategories(array $categories, array $dataType)
    {
        $resultCategories = [];

        foreach ($dataType as $type) {
            $postfix = '_' . $this->convertDataTypeText($type);

            foreach ($categories as $cat) {
                array_push($resultCategories, $cat . $postfix);
            }
        }
        return $resultCategories;
    }

    protected function convertCmLargeGenres(?array $cmLargeGenres): array
    {
        $names = $this->getCmLargeGenreNames($cmLargeGenres) ?: '全ジャンル';
        $cmLargeGenreNames = [
            'ジャンル:',
            $names,
        ];

        return $cmLargeGenreNames;
    }

    protected function addPostFixCategory(array $categories, ?int $type = 1)
    {
        return $categories;
    }

    private function convertCmMaterials(?array $cmIds): array
    {
        $cmMaterials = $this->getCmMaterials($cmIds) ?: '全CM';
        // CM素材別（権限から取得）
        $cmSettingNamesHead = [
            'CM素材別:',
            $cmMaterials,
        ];
        return $cmSettingNamesHead;
    }

    private function convertProgramNames(?array $progIds): array
    {
        $programs = $this->getProgramNames($progIds) ?: '指定なし';
        $programNames = [
            '番組名:',
            $programs,
        ];
        return $programNames;
    }

    private function convertCmTypeFlag(?string $cmType): array
    {
        $cmType = [
            'CM種別:',
            \Config::get('const.CM_TYPE')[$cmType],
        ];
        return $cmType;
    }

    private function convertCmSeconds(string $cmSeconds): array
    {
        $cmSecond = [
            'CM秒数:',
            \Config::get('const.CM_SECONDS')[$cmSeconds],
        ];
        return $cmSecond;
    }

    private function convertPeriod(string $startDate, string $endDate, string $startTime, string $endTime, ?bool $timeFlag = true): array
    {
        $dow = \Config::get('const.DOW');
        $sd = new Carbon($startDate);
        $ed = new Carbon($endDate);
        $sdf = $sd->format('Y年m月d日(' . $dow[$sd->dayOfWeek] . ')');
        $edf = $ed->format('Y年m月d日(' . $dow[$ed->dayOfWeek] . ')');
        $stHour = (int) (substr($startTime, 0, 2));

        if (0 <= $stHour && $stHour <= 4) {
            $stHour = $stHour + 24;
        }
        $etHour = (int) (substr($endTime, 0, 2));

        if (0 <= $etHour && $etHour <= 4) {
            $etHour = $etHour + 24;
        }
        $st = $et = '';

        if ($timeFlag) {
            $st = $stHour . ':' . substr($startTime, 2, 2);
            $et = $etHour . ':' . substr($endTime, 2, 2);
        }
        $period = [
            '検索期間:',
            $sdf . ' ' . $st . '～' . $edf . ' ' . $et,
        ];
        return $period;
    }

    private function convertPeriodDivision(string $period): array
    {
        switch ($period) {
            case 'period':
                $result = '期間計';
                break;
            case 'day':
                $result = '日別';
                break;
            case 'week':
                $result = '週別';
                break;
            case 'month':
                $result = '月別';
                break;
            case 'cm':
                $result = 'CM別';
                break;
        }

        $period = [
            '期間区切り:',
            $result,
        ];
        return $period;
    }

    private function convertTimePeriod(string $startTime, string $endTime): array
    {
        $stHour = substr($startTime, 0, 2);

        if (0 <= $stHour && $stHour <= 4) {
            $stHour = $stHour + 24;
        }

        $etHour = substr($endTime, 0, 2);

        if (0 <= $etHour && $etHour <= 4) {
            $etHour = $etHour + 24;
        }

        $st = $stHour . ':' . substr($startTime, 2, 2);
        $et = $etHour . ':' . substr($endTime, 2, 2);

        $timePeriod = [
            '時間帯',
            $st . '～' . $et,
        ];

        return $timePeriod;
    }

    private function convertDow(array $dows, bool $isHoliday): array
    {
        $dow = \Config::get('const.DOW');

        $str = '';

        foreach ($dow as $key => $val) {
            if (in_array($key, $dows)) {
                $str .= $val;
            }
        }

        if ($isHoliday) {
            $str .= '（祝日含む）';
        } else {
            $str .= '（祝日含まない）';
        }

        $dispDow = [
            '曜日',
            $str,
        ];

        return $dispDow;
    }

    private function convertGenres(?array $genres): array
    {
        $result = [
            'ジャンル',
        ];

        if (empty($genres)) {
            $result[] = '全ジャンル';
        } else {
            $list = $this->searchConditionTextDao->getGenres($genres);
            $genreArr = [];

            foreach ($list as $row) {
                $exp = explode(' ', $row->name);
                $genreArr[] = $exp[0];
            }
            $result[] = implode('、', $genreArr);
        }

        return $result;
    }

    private function convertProgType(?array $programTypes)
    {
        $result = [
            '番組タイプ',
        ];

        $map = \Config::get('const.PROGRAM_TYPES_MAP');

        foreach ($programTypes as $val) {
            $result[] = $map[$val];
        }

        return $result;
    }

    private function convertReportDateTime()
    {
        $dow = \Config::get('const.DOW');
        $created = [
            'レポート作成日時:',
            Carbon::now()->format('Y年m月d日(' . $dow[Carbon::today()->dayOfWeek] . ') H:i'),
        ];

        return $created;
    }

    private function convertProfileMainChannel(int $channelId, int $regionId): array
    {
        if ($regionId === 1) {
            $channelList = \Config::get('const.DIGITAL_KANTO_CM');
        } elseif ($regionId === 2) {
            $channelList = \Config::get('const.CHANNELS_KANSAI');
        }

        $mainChannelHead = [
            'メイン放送局:',
            $channelList[$channelId],
        ];

        return $mainChannelHead;
    }

    private function convertProgramListChannels(array $channels, string $digitalAndBs, int $regionId): array
    {
        if ($digitalAndBs == 'digital') {
            if ($regionId === 1) {
                $channelNames = \Config::get('const.DIGITAL_KANTO_CM');
                $allName = '地デジ 7放送';
            } else {
                $channelNames = \Config::get('const.CHANNELS_KANSAI');
                $allName = '地デジ 6放送';
            }
        } elseif ($digitalAndBs == 'bs1') {
            $channelNames = \Config::get('const.BS_1');
            $allName = 'BS1 7放送';
        } elseif ($digitalAndBs == 'bs2') {
            $channelNames = \Config::get('const.BS_2');
            $allName = 'BS2 4放送';
        }

        if (count($channels) != count($channelNames)) {
            $arr = [];

            foreach ($channels as $id) {
                $arr[] = $channelNames[$id];
            }
            $result = implode('、', $arr);
        } else {
            $result = $allName;
        }

        return [
            '放送',
            $result,
        ];
    }

    private function convert15SecFlag(string $conv15SecFlag): array
    {
        $conv15SecHead = [
            '15秒換算:',
            $conv15SecFlag == 1 ? \Config::get('const.CONV_15_SEC_FLAG')[1] : \Config::get('const.CONV_15_SEC_FLAG')[0],
        ];

        return $conv15SecHead;
    }

    private function convertCategoryHead(int $conditionCrossCount, string $division, ?array $codes, string $startDate, string $endDate, string $startTime, string $endTime, string $regionId, ?bool $csvFlag = null, ?array $dataType = null, ?int $tsConditionCrossCount = null, ?string $selected = null): array
    {
        if ($dataType === null) {
            // TODO - fujisaki: 現状ランキングにデータ種別は存在しないので、dataTypegがなかった時は必ずリアルタイムが入るようにする。
            $dataType = [\Config::get('const.DATA_TYPE_NUMBER.REALTIME')];
        }

        $isConditionCross = $division == 'condition_cross';
        $st = substr($startTime, 0, 2) . ':' . substr($startTime, 2, 2);
        $et = substr($endTime, 0, 2) . ':' . substr($endTime, 2, 2);

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($dataType);

        $hasSamples = [];
        $hasSamples['rt'] = $hasSamples['ts'] = false;

        if ($dataType === null || $isRt) {
            $hasSamples['rt'] = true;
        }

        if ($isTs || $isGross || $isTotal || $isRtTotal) {
            $hasSamples['ts'] = true;
        }

        if ($csvFlag === true) {
            $categoryHead['head'] = [
                'サンプル:',
                \Config::get('const.REGION')[$regionId],
            ];
        } else {
            $categoryHead['head'] = [
                'サンプル（' . \Config::get('const.REGION')[$regionId] . '）:',
            ];
        }

        $rtSamplesHead = $tsSamplesHead = [];

        foreach ($hasSamples as $name => $has) {
            if ($has === false) {
                continue;
            }

            if ($name === 'rt') {
                $isNameRt = true;
                $useConditionCrossCount = $conditionCrossCount;
            } else {
                $isNameRt = false;
                $useConditionCrossCount = $tsConditionCrossCount;
            }

            $samplesHead = [];

            if ($isConditionCross) {
                $numbers = $useConditionCrossCount;
            } elseif (in_array($division, \Config::get('const.BASE_DIVISION')) && count($codes) > 0) {
                $numbers = $this->getBasicNumbers($division, $codes, $startDate, $endDate, $st, $et, $regionId, $isNameRt);
            } elseif (count($codes) > 0) {
                $numbers = $this->getOriginalNumbers($division, $codes, $startDate, $endDate, $st, $et, $regionId, $isNameRt);
            }
            $phNumbers = $this->getPersonalHouseholdNumbers($startDate, $endDate, $st, $et, $regionId, $isNameRt);

            if (!empty($selected)) {
                if ($selected == 'personal') {
                    // 個人
                    $samplesHead[] = '個人 ' . number_format($phNumbers['personal']);
                } elseif ($selected == 'household') {
                    $samplesHead[] = '世帯 ' . number_format($phNumbers['household']);
                } elseif ($isConditionCross) {
                    $samplesHead[] = '掛け合わせ ' . number_format($useConditionCrossCount);
                } else {
                    foreach ($numbers as $row) {
                        if ($row->code == $selected) {
                            $samplesHead[] = $row->name . ' ' . number_format($row->number);
                            break;
                        }
                    }
                }
                $samplesHead[] = '';
                $samplesHead[] = '＜指定期間構成＞';
                $samplesHead[] = '';
            }

            // 個人

            // 選択したアスペクトのカテゴリ名を取得して人数と連結
            if ($isConditionCross) {
                $samplesHead[] = '掛け合わせ' . ' ' . number_format($useConditionCrossCount);
                $samplesHead[] = '世帯 ' . number_format($phNumbers['household']);
            } else {
                if (in_array('personal', $codes)) {
                    $samplesHead[] = '個人 ' . number_format($phNumbers['personal']);
                }

                foreach ($numbers as $row) {
                    $samplesHead[] = $row->name . ' ' . number_format($row->number);
                }

                if (in_array('household', $codes)) {
                    $samplesHead[] = '世帯 ' . number_format($phNumbers['household']);
                }
            }

            if ($isNameRt) {
                $rtSamplesHead = [
                    'head' => $samplesHead,
                ];
            } else {
                $tsSamplesHead = [
                    'head' => $samplesHead,
                ];
            }
        }

        if ($hasSamples['rt'] === true) {
            $categoryHead = array_merge_recursive($categoryHead, $rtSamplesHead);
            $categoryHead['head'][] = '';
        }

        if ($hasSamples['rt'] === true && $hasSamples['ts'] === true && $csvFlag !== true) {
            $categoryHead['head'][] = '/';
            $categoryHead['head'][] = '';
        }

        if ($hasSamples['ts'] === true) {
            $categoryHead = array_merge_recursive($categoryHead, $tsSamplesHead);
            $categoryHead['head'][] = '';
        }

        $categoryHead['numbers'] = $numbers;

        return $categoryHead;
    }

    private function convertUnit(?string $value = '%')
    {
        $unit = [
            '単位:',
            $value,
        ];

        return $unit;
    }

    private function convertAllChanelsText(?string $allChannels)
    {
        $result = ['選択局全表示:'];

        if ($allChannels === 'true' || $allChannels === '1') {
            $result[] = 'する';
        } else {
            $result[] = 'しない';
        }
        return $result;
    }

    private function convertAxisTypeText(?string $axisType): array
    {
        $axisType = [
            '集計軸:',
            \Config::get('const.AXIS_TYPE')[$axisType],
        ];
        return $axisType;
    }

    private function getCompanyNames(?array $companyIds): string
    {
        $result = [];

        $hashMap = [];

        if (!empty($companyIds)) {
            $list = $this->searchConditionTextDao->getCompanyNames($companyIds);

            foreach ($list as $row) {
                $hashMap[$row->id] = $row->name;
            }

            foreach ($companyIds as $id) {
                array_push($result, $hashMap[$id]);
            }
        }

        return implode('、', $result);
    }

    private function getProductNames(?array $productIds): string
    {
        $result = [];

        if (!empty($productIds)) {
            $list = $this->searchConditionTextDao->getProductNames($productIds);

            foreach ($list as $row) {
                array_push($result, $row->name);
            }
        }

        return implode('、', $result);
    }

    private function getProgramNames(?array $programIds): string
    {
        $result = [];

        if (!empty($programIds)) {
            $list = $this->searchConditionTextDao->getProgramNames($programIds);

            foreach ($list as $row) {
                array_push($result, $row->title);
            }
        }

        return implode('、', $result);
    }

    private function getCmMaterials(?array $cmMaterialIds): string
    {
        $result = [];

        if (!empty($cmMaterialIds)) {
            $list = $this->searchConditionTextDao->getCmMaterials($cmMaterialIds);

            foreach ($list as $row) {
                array_push($result, $row->cm_id . '（' . $row->setting . '）');
            }
        }

        return implode('、', $result);
    }

    private function getBasicNumbers(string $division, array $codes, string $startDate, string $endDate, string $startTime, string $endTime, int $regionId, bool $isRt = true): array
    {
        $result = $this->searchConditionTextDao->getBasicNumbers($division, $codes, $startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $regionId, $isRt);
        return $result;
    }

    private function getOriginalNumbers(string $division, array $codes, string $startDate, string $endDate, string $startTime, string $endTime, int $regionId, bool $isRt = true): array
    {
        $result = $this->searchConditionTextDao->getOriginalNumbers($division, $codes, $startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $regionId, $isRt);
        return $result;
    }

    private function getCmLargeGenreNames(?array $cmLargeGenres): string
    {
        $result = [];

        if (!empty($cmLargeGenres)) {
            $list = $this->searchConditionTextDao->getCmLargeGenreNames($cmLargeGenres);

            foreach ($list as $row) {
                array_push($result, $row->name);
            }
        }

        return implode('、', $result);
    }

    private function getChannelCodeNames($channelIds): array
    {
        $list = $this->searchConditionTextDao->getChannelCodeNames($channelIds);

        $channelCodeNames = [];

        foreach ($list as $value) {
            $channelCodeNames[$value->id] = $value->code_name;
        }

        return $channelCodeNames;
    }

    private function convertCrossConditionText(array $conditionCross): array
    {
        $string = '';

        // 性別
        $string .= '性別（';

        if (count($conditionCross['gender']) > 0) {
            foreach ($conditionCross['gender'] as $gender) {
                if ($gender === 'f') {
                    $string .= '女性';
                } elseif ($gender === 'm') {
                    $string .= '男性';
                } elseif ($gender === null) {
                    $string .= 'ALL';
                }
                $string .= '、';
            }
            $string = rtrim($string, '、');
        }
        $string .= '）、';
        // 年齢
        if (array_key_exists('age', $conditionCross)) {
            $string .= '年齢（' . $conditionCross['age']['from'] . '-' . $conditionCross['age']['to'] . '）、';
        }
        // 職業
        $string .= '職業（';

        if (array_key_exists('occupation', $conditionCross)) {
            if (count($conditionCross['occupation']) > 0) {
                foreach ($conditionCross['occupation'] as $occupation) {
                    switch ($occupation) {
                        case '2':
                            // case '6':
                            // case '7':
                            $string .= '経営・自営、';
                            break;
                        case '1':
                            // case '3':
                            // case '4':
                            // case '5':
                            $string .= '会社員、';
                            break;
                        case '8':
                            $string .= '主婦、';
                            break;
                        case '9':
                            $string .= 'パート・バイト、';
                            break;
                        case '10':
                            $string .= '未就学児、';
                            break;
                        case '11':
                            $string .= '小学生、';
                            break;
                        case '12':
                            // case '13':
                            $string .= '中・高校生、';
                            break;
                        case '14':
                            // case '15':
                            $string .= '大学（院）生、';
                            break;
                        case '16':
                            // case '17':
                            // case '18':
                            $string .= 'その他・無職、';
                            break;
                        case '':
                            $string .= 'ALL';
                            break;
                    }
                }
            } else {
                $string .= 'ALL';
            }
            $string = rtrim($string, '、');
        } else {
            $string .= 'ALL';
        }
        $string .= '）、';
        // 未既婚
        $string .= '未既婚（';

        if (count($conditionCross['married']) > 0) {
            foreach ($conditionCross['married'] as $married) {
                if ($married === '1') {
                    $string .= '未婚';
                    $string .= '、';
                } elseif ($married === '2') {
                    $string .= '既婚';
                    $string .= '、';
                } elseif ($married === null) {
                    $string .= 'ALL';
                }
            }
            $string = rtrim($string, '、');
        }
        $string .= '）';
        // 子供条件
        if (isset($conditionCross['child']) && $conditionCross['child']['enabled'] && $conditionCross['child']['enabled'] !== 'false') {
            // 性別
            $string .= '、子供性別（';

            if (count($conditionCross['child']['gender']) > 0) {
                foreach ($conditionCross['child']['gender'] as $gender) {
                    if ($gender === 'f') {
                        $string .= '女性';
                    } elseif ($gender === 'm') {
                        $string .= '男性';
                    } elseif ($gender === null) {
                        $string .= 'ALL';
                    }
                    $string .= '、';
                }
                $string = rtrim($string, '、');
            }
            $string .= '）';
            // 年齢
            if (array_key_exists('age', $conditionCross['child'])) {
                $string .= '、子供年齢（' . $conditionCross['child']['age']['from'] . '-' . $conditionCross['child']['age']['to'] . '）';
            }
        }

        $conditionCrossString = [
            'サンプル条件:',
            $string,
        ];

        return $conditionCrossString;
    }
}
