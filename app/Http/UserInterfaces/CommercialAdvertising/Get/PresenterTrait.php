<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

trait PresenterTrait
{
    protected function createTableData(array $data, array $channels, string $csvFlag): array
    {
        // 出稿数表を作成する
        $list = json_decode(json_encode($data), true);

        $hash = [];
        $aggregate = [];
        // チャンネル、曜日、時間でハッシュマップ化
        foreach ($list as $row) {
            $channelId = $row['channel_id'];
            $dow = $row['dow'];
            $hour = $row['hh'] >= 5 ? $row['hh'] : $row['hh'] + 24; // 28時間表記
            if (empty($hash[$channelId])) {
                $hash[$channelId] = [];
            }

            if (empty($hash[$channelId][$dow])) {
                $hash[$channelId][$dow] = [];
            }

            if (empty($hash[$channelId][$dow][$hour])) {
                $hash[$channelId][$dow][$hour] = $row['count'];

                if (empty($aggregate[$channelId])) {
                    $aggregate[$channelId] = $row['count'];
                } else {
                    $aggregate[$channelId] += $row['count'];
                }
            }
        }

        $result = [];

        $dows = [
            1,
            2,
            3,
            4,
            5,
            6,
            0,
        ]; // 曜日

        // 時間
        $hours = array_map(function ($el) {
            return str_pad($el, 2, 0, STR_PAD_LEFT);
        }, range(5, 28));
        // 時間ループ
        foreach ($hours as $hour) {
            $rowArray = [];
            $rowArray['hour'] = $hour;
            // チャンネルループ
            foreach ($channels as $channel) {
                // 曜日ループ
                foreach ($dows as $dow) {
                    $count = $this->hashExists($hash, $channel, $dow, $hour) ? $hash[$channel][$dow][$hour] : '';
                    $rowArray[$channel . $dow] = $count;
                }

                if ($csvFlag) {
                    $rowArray[] = '';
                }
            }

            if ($csvFlag === '1') {
                $rowArray['hour'] = $rowArray['hour'] . ':00';
            }
            array_push($result, $rowArray);
        }

        if ($csvFlag) {
            $rowArray = ['Ch計'];

            foreach ($channels as $channel) {
                $total = $aggregate[$channel] ?? '0';
                $rowArray = array_merge($rowArray, [$total, '', '', '', '', '', '', '']);
            }
            array_push($result, $rowArray);
        }

        return [
            'list' => $result,
            'aggregate' => $aggregate,
        ];
    }

    protected function hashExists(array $hash, String $channel, String $dow, String $hour): bool
    {
        if (empty($hash[$channel])) {
            return false;
        }

        if (empty($hash[$channel][$dow])) {
            return false;
        }

        if (empty($hash[$channel][$dow][$hour])) {
            return false;
        }
        return true;
    }
}
