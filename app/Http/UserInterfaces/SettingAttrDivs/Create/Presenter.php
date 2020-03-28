<?php

namespace App\Http\UserInterfaces\SettingAttrDivs\Create;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\AttrDivCreationLimitOverException;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputData;

class Presenter implements OutputBoundary
{
    private $presenterOutput;

    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     * @throws AttrDivCreationLimitOverException
     */
    public function __invoke(OutputData $output)
    {
        if (!$output->isSuccess()) {
            throw new AttrDivCreationLimitOverException();
        }

        return $this->presenterOutput->set(response(null, 204));
    }
}
