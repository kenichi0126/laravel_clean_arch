<?php

namespace App\Http\UserInterfaces\PanelStructure\Get;

use stdClass;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputData;

class ListPresenter implements OutputBoundary
{
    private $presenterOutput;

    /**
     * ListPresenter constructor.
     * @param PresenterOutput $presenterOutput
     */
    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        if (empty($output->attrDivs()) && empty($output->panelData())) {
            abort(404);
        }

        $convertedData = [];
        $baseDivision = '';

        foreach ($output->panelData() as $key => $value) {
            if (empty($value->paneler_id)) {
                $value->paneler_id = [];
            } else {
                $value->paneler_id = ltrim($value->paneler_id, '{');
                $value->paneler_id = rtrim($value->paneler_id, '}');
                $value->paneler_id = explode(',', $value->paneler_id);
            }

            // ベースとなる集計区分
            if (isset($output->attrDivs()['base'][$value->division])) {
                if (!isset($output->attrDivs()['base'][$value->division][$value->code])) {
                    $baseDivision = $value->division;
                    $convertedData['common'][$value->code] = $value->number;
                    continue;
                }

                $codeData = [];
                $codeData['code'] = $value->code;
                $codeData['name'] = $output->attrDivs()['base'][$value->division][$value->code]['name'];

                if ($output->baseFiveDivisionFlag()) {
                    if (!empty($output->attrDivs()['base'][$value->division][$value->code]['definition_text'])) {
                        foreach ($output->attrDivs()['base'][$value->division][$value->code]['definition_text'] as $textData) {
                            $codeData[explode('=', $textData->def)[0]] = $this->convertDefinitionText($textData, false);
                        }
                    }

                    if (($value->division === 'ga8' || $value->division === 'gm') && ($value->code === 'c' || $value->code === 't')) {
                        $codeData['gender'] = '男女';
                    }
                } else {
                    $attrDivTextData = [];

                    if (!empty($output->attrDivs()['base'][$value->division][$value->code]['definition_text'])) {
                        foreach ($output->attrDivs()['base'][$value->division][$value->code]['definition_text'] as $textData) {
                            $attrDivTextData[] = $this->convertDefinitionText($textData, true);
                        }
                    }

                    if (!empty($output->attrDivs()['base'][$value->division][$value->code]['restore_info_text'])) {
                        $attrDivTextData[] = $output->attrDivs()['base'][$value->division][$value->code]['restore_info_text'];
                    }
                    $codeData['info_text'] = $attrDivTextData;
                }

                $codeData['number'] = $value->number;
                $codeData['paneler_id'] = $value->paneler_id;
                $isCustomDivision = $output->baseFiveDivisionFlag() === false;
                $codeData['is_custom_division'] = $isCustomDivision;

                $displayOrder = $output->attrDivs()['base'][$value->division][$value->code]['display_order'];
                $convertedData['base'][$value->division][$displayOrder] = $codeData;
            }

            // カスタム区分
            if (isset($output->attrDivs()['custom'][$value->division], $output->attrDivs()['custom'][$value->division][$value->code])) {
                $codeData = [];
                $codeData['name'] = $output->attrDivs()['custom'][$value->division][$value->code]['name'];

                $attrDivTextData = [];

                if (!empty($output->attrDivs()['custom'][$value->division][$value->code]['definition_text'])) {
                    foreach ($output->attrDivs()['custom'][$value->division][$value->code]['definition_text'] as $textData) {
                        $attrDivTextData[] = $this->convertDefinitionText($textData, true);
                    }
                }

                if (!empty($output->attrDivs()['custom'][$value->division][$value->code]['restore_info_text'])) {
                    $attrDivTextData[] = $output->attrDivs()['custom'][$value->division][$value->code]['restore_info_text'];
                }

                $codeData['info_text'] = $attrDivTextData;

                $codeData['number'] = $value->number;
                $codeData['paneler_id'] = $value->paneler_id;

                $displayOrder = $output->attrDivs()['custom'][$value->division][$value->code]['display_order'];
                $convertedData['custom'][$value->division][$displayOrder] = $codeData;
            }
        }
        ksort($convertedData['base'][$baseDivision]);

        $response = [
            'base' => $convertedData['base'],
            'common' => $convertedData['common'],
        ];

        if (!empty($convertedData['custom'])) {
            foreach ($convertedData['custom'] as $division => $codes) {
                foreach ($codes as $displayOrder => $custom) {
                    foreach ($convertedData['base'][$baseDivision] as $baseDisplayOrder => $base) {
                        $convertedData['custom'][$division][$displayOrder]['base_code'][$base['code']] = count(array_intersect($custom['paneler_id'], $base['paneler_id']));
                    }
                }
                ksort($convertedData['custom'][$division]);
            }
            $response['custom'] = $convertedData['custom'];
        }

        $this->presenterOutput->set($response);
    }

    /**
     * @param stdClass $definitionText
     * @param bool $isCustomDivision [description]
     * @return string                     [description]
     */
    private function convertDefinitionText(stdClass $definitionText, bool $isCustomDivision): String
    {
        $split = explode('=', $definitionText->def);

        if ($split[0] === 'age') {
            $prefix = '';

            if ($isCustomDivision) {
                $prefix = '年齢： ';
            }
            return $prefix . $split[1] . '才';
        }

        $split = explode('=', $definitionText->condition_text);

        if (isset($split[1])) {
            if ($isCustomDivision) {
                $prefix = '';

                if ($split[0] === '性別' || $split[0] === '職業' || $split[0] === '未既婚') {
                    $prefix = $split[0] . '： ';
                }
                return $prefix . $split[1];
            }
            return $split[1];
        }
        return '';
    }
}
