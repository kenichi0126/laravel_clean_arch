<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SearchableNumberOfDaysValidator implements Rule
{
    private $number;

    private $configNames;

    public function __construct(array $configNames)
    {
        $this->configNames = $configNames;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($this->configNames as $configName) {
            if (in_array($value['division'], \Config::get('const.BASE_DIVISION'))) {
                $this->number = \Config::get('const.SEARCH_PERIOD_LIMIT.' . $configName . '.BASIC');
            } else {
                $this->number = \Config::get('const.SEARCH_PERIOD_LIMIT.' . $configName . '.CUSTOM');
            }

            if ($value['requestPeriod'] > $this->number) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '期間は' . $this->number . '日以内で指定してください。';
    }
}
