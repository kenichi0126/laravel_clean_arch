<?php

namespace Switchm\Php\Illuminate\Foundation\Http;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    protected $inputData;

    /**
     * @return mixed
     */
    public function inputData()
    {
        return $this->inputData;
    }
}
