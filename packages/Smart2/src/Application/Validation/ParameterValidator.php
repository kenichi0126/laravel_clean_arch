<?php

namespace Smart2\Application\Validation;

class ParameterValidator
{
    /**
     * バイト数チェック.
     *
     * 半角1バイト、全角2バイトで計算し、
     * 指定した値を超過している場合はエラーとする。
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateStrLenOver($attribute, $value, $parameters)
    {
        $num = $parameters[0];

        // パラメータが不正の場合は強制的にエラー
        if (!isset($num) && ctype_digit($num)) {
            return false;
        }

        $num = (int) $num;

        // 指定のバイト数を超えている場合はエラー
        if ($num < strlen(mb_convert_encoding($value, 'SJIS', 'UTF-8'))) {
            return false;
        }

        return true;
    }

    /**
     * カンマかダブルクォート含みチェック.
     *
     * カンマかダブルクォートがパラメータに含まれている場合はエラーとする。
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateStrInCommaOrDoublequote($attribute, $value, $parameters)
    {
        if (strpos($value, ',') !== false
            || strpos($value, '"') !== false) {
            return false;
        }

        return true;
    }
}
