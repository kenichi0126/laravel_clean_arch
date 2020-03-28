<?php

namespace App\Http\UserInterfaces\ProgramTable\Get;

use App\Rules\SearchableBoundaryValidator;
use Carbon\Carbon;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputData;

class Request extends FormRequest
{
    use ValidatorSetterTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

    public function messages()
    {
        return [];
    }

    public function prepareForValidation(): void
    {
        $this->merge($this->SearchableBoundaryValidatorField());
    }

    /**
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        \UserInfo::execute(\Auth::id());

        if (!\Auth::getUser()->isDuringTrial($this->input('startDateTime'), $this->input('endDateTime'))) {
            throw new TrialException(\Auth::getUser());
        }

        $period = getRdbDwhSearchPeriod(new Carbon($this->input('startDateTime')), new Carbon($this->input('endDateTime')));

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('digitalAndBs'),
            $this->input('digitalKanto'),
            $this->input('bs1'),
            $this->input('bs2'),
            $this->input('regionId'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('draw'),
            $this->input('codes'),
            $this->input('channels'),
            $this->input('dispPeriod'),
            \Config::get('const.BASE_DIVISION'),
            $period,
            \Auth::id()
        );
    }
}
