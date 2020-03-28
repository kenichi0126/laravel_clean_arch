<?php

namespace Switchm\SmartApi\Components\Common;

class CreateTableData
{
    /**
     * @param array $data
     * @param array $channelIds
     * @param string $alias
     * @param string $dataDivision
     * @param string $csvFlag
     * @param string $channelType
     * @return array
     */
    public function __invoke(array $data, array $channelIds, string $alias, string $dataDivision, string $csvFlag, string $channelType): array
    {
        // 時間帯別視聴データを作成する
        $list = json_decode(json_encode($data), true);
        $hash = [];

        // 時間、曜日、チャンネルでハッシュマップ化する
        foreach ($list as $row) {
            $channelId = $row['channel_id'];
            $dow = $row['dow'];
            $hour = $row['hhmm'] >= 5 ? $row['hhmm'] : $row['hhmm'] + 24; // 28時間表記

            if (empty($hash[$channelId])) {
                $hash[$channelId] = [];
            }

            if (empty($hash[$channelId][$dow])) {
                $hash[$channelId][$dow] = [];
            }

            if (empty($hash[$channelId][$dow][$hour])) {
                $hash[$channelId][$dow][$hour] = $row[$alias];
            }
        }
        $result = [];

        $channels = $channelIds;

        $dows = [
            1,
            2,
            3,
            4,
            5,
            6,
            0,
        ]; // 曜日

        $avgHash = [];

        // 時間
        $hours = array_map(function ($el) {
            return str_pad($el, 2, 0, STR_PAD_LEFT);
        }, range(5, 28));

        $digit = 0;
        // Rating は 第一
        if ($dataDivision === 'viewing_rate') {
            $digit = 1;
        } elseif ($csvFlag === '0') {
            // その他はcsv でなければ 0
            $digit = 0;
        } else {
            $digit = 1;
        }

        if (strpos($channelType, 'bs') !== false) {
            // BSの場合は必ず2桁
            $digit = 2;
        }

        // 時間ループ
        foreach ($hours as $hour) {
            $rowArray = [];
            // 曜日ループ
            $rowArray['hour'] = $hour;

            foreach ($dows as $dow) {
                // チャンネルループ
                foreach ($channels as $channel) {
                    if (!$this->avgHashExists($hash, $channel, $dow, $hour)) {
                        $rowArray[$channel . $dow] = '';
                        continue;
                    }
                    $rate = $this->avgHashExists($hash, $channel, $dow, $hour) ? $hash[$channel][$dow][$hour] : 0;

                    if (empty($avgHash[$channel])) {
                        $avgHash[$channel] = [];
                    }

                    if (empty($avgHash[$channel][$dow])) {
                        $avgHash[$channel][$dow] = [];
                    }

                    // 全日(6:00-23:59)対応
                    if ($hour > 5 && $hour < 24) {
                        if (!isset($avgHash[$channel][$dow]['all'])) {
                            $avgHash[$channel][$dow]['all'] = 0;
                            $avgHash[$channel][$dow]['all_count'] = 0;
                        }
                        $avgHash[$channel][$dow]['all'] += (float) $rate;
                        $avgHash[$channel][$dow]['all_count'] += 1;
                    }

                    if ($hour > 18 && $hour < 22) {
                        if (!isset($avgHash[$channel][$dow]['gold'])) {
                            $avgHash[$channel][$dow]['gold'] = 0;
                            $avgHash[$channel][$dow]['gold_count'] = 0;
                        }
                        $avgHash[$channel][$dow]['gold'] += (float) $rate;
                        $avgHash[$channel][$dow]['gold_count'] += 1;
                    }

                    if ($hour > 18 && $hour < 23) {
                        if (!isset($avgHash[$channel][$dow]['night'])) {
                            $avgHash[$channel][$dow]['night'] = 0;
                            $avgHash[$channel][$dow]['night_count'] = 0;
                        }
                        $avgHash[$channel][$dow]['night'] += (float) $rate;
                        $avgHash[$channel][$dow]['night_count'] += 1;
                    }

                    if ($hour > 4 && $hour < 29) {
                        if (!isset($avgHash[$channel][$dow]['avg'])) {
                            $avgHash[$channel][$dow]['avg'] = 0;
                            $avgHash[$channel][$dow]['avg_count'] = 0;
                        }
                        $avgHash[$channel][$dow]['avg'] += (float) $rate;
                        $avgHash[$channel][$dow]['avg_count'] += 1;
                    }

                    $rowArray[$channel . $dow] = round($rate, $digit);
                }
            }

            if ($csvFlag === '1') {
                $rowArray['hour'] = $rowArray['hour'] . ':00';
            }
            array_push($result, $rowArray);
        }

        // $avgHashをループさせキー値を取得する
        $allRowArray = [];
        $goldRowArray = [];
        $nightRowtArray = [];
        $avgRowArray = [];

        foreach ($dows as $dow) {
            $allRowArray['hour'] = 'all';
            $goldRowArray['hour'] = 'G';
            $nightRowtArray['hour'] = 'P';
            $avgRowArray['hour'] = 'Av';

            foreach ($channels as $channel) {
                $allRate = ($this->avgHashExists($avgHash, $channel, $dow, 'all') ? round(($avgHash[$channel][$dow]['all'] / $avgHash[$channel][$dow]['all_count']), $digit) : '');
                $goldRate = ($this->avgHashExists($avgHash, $channel, $dow, 'gold') ? round(($avgHash[$channel][$dow]['gold'] / $avgHash[$channel][$dow]['gold_count']), $digit) : '');
                $nightRate = ($this->avgHashExists($avgHash, $channel, $dow, 'night') ? round(($avgHash[$channel][$dow]['night'] / $avgHash[$channel][$dow]['night_count']), $digit) : '');
                $avgRate = ($this->avgHashExists($avgHash, $channel, $dow, 'avg') ? round(($avgHash[$channel][$dow]['avg'] / $avgHash[$channel][$dow]['avg_count']), $digit) : '');
                // キー値を設定する
                $allRowArray[$channel . $dow] = $allRate;
                $goldRowArray[$channel . $dow] = $goldRate;
                $nightRowtArray[$channel . $dow] = $nightRate;
                $avgRowArray[$channel . $dow] = $avgRate;
            }
        }

        if ($csvFlag === '1') {
            $allRowArray['hour'] = '全日(6:00-23:59)';
            $goldRowArray['hour'] = 'G(19:00-21:59)';
            $nightRowtArray['hour'] = 'P(19:00-22:59)';
            $avgRowArray['hour'] = 'AVG(5:00-28:59)';
        }
        array_push($result, $allRowArray);
        array_push($result, $goldRowArray);
        array_push($result, $nightRowtArray);
        array_push($result, $avgRowArray);

        return $result;
    }

    /**
     * @param array $avgHash
     * @param string $channel
     * @param string $dow
     * @param string $key
     * @return bool
     */
    protected function avgHashExists(array $avgHash, String $channel, String $dow, String $key): bool
    {
        if (empty($avgHash[$channel])) {
            return false;
        }

        if (empty($avgHash[$channel][$dow])) {
            return false;
        }

        if (!isset($avgHash[$channel][$dow][$key])) {
            return false;
        }

        return true;
    }
}
