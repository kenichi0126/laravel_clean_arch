<?php

namespace Switchm\SmartApi\Components\Common;

trait ValidatorSetterTrait
{
    /**
     * @return array
     */
    public function SearchableNumberOfDaysValidatorField()
    {
        return [
            'SearchableNumberOfDaysValidator' => [
                'division' => $this->input('division'),
                'requestPeriod' => $this->input('dateRange'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function SearchableBoundaryValidatorField()
    {
        return [
            'searchableBoundaryValidator' => [
                'startDateTime' => $this->input('startDateTime'),
                'endDateTime' => $this->input('endDateTime'),
                'dataType' => $this->input('dataType'),
                'regionId' => $this->input('regionId'),
            ],
        ];
    }
}
