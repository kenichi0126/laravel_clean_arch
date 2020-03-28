<?php

namespace Switchm\SmartApi\Components\ProgramNames\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\ProgramNamesDao;

class Interactor implements InputBoundary
{
    private $programNamesDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param ProgramNamesDao $programNamesDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(ProgramNamesDao $programNamesDao, OutputBoundary $outputBoundary)
    {
        $this->programNamesDao = $programNamesDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $split = mb_split(' ', mb_convert_kana($inputData->programName(), 'RNAs'));
        $programNames = [];

        foreach ($split as &$val) {
            $val = trim($val);

            if (!empty($val)) {
                $programNames[] = '%' . $val . '%';
            }
        }

        // 放送
        $channels = $inputData->channels();

        $digitalAndBs = $inputData->digitalAndBs();
        $bsFlg = false;

        if ($inputData->programFlag() === true) {
            if ($digitalAndBs === 'digital') {
                $channels = $inputData->digitalKanto();
            } elseif ($digitalAndBs === 'bs1') {
                $channels = $inputData->bs1();
                $bsFlg = true;
            } elseif ($digitalAndBs === 'bs2') {
                $channels = $inputData->bs2();
                $bsFlg = true;
            }
        }

        $params = [
            'title' => $programNames,
            'startDate' => $inputData->startDate(),
            'endDate' => $inputData->endDate(),
            'startTime' => $inputData->startTimeShort(),
            'endTime' => $inputData->endTimeShort(),
            'cmType' => $inputData->cmType(),
            'cmSeconds' => $inputData->cmSeconds(),
            'productIds' => $inputData->productIds(),
            'channel' => $channels,
            'companyIds' => $inputData->companies(),
            'regionId' => $inputData->regionId(),
            'programIds' => $inputData->programIds(),
            'programFlag' => $inputData->programFlag(),
            'bsFlag' => $bsFlg,
            'wdays' => $inputData->wdays(),
            'holiday' => $inputData->holiday(),
        ];
        // 0時跨ぎ対応（開始時刻のほうが大きくなる場合）
        $straddlingFlg = $inputData->straddlingFlg();

        $data = $this->programNamesDao->findProgramNames($params, $straddlingFlg);

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
