<?php

namespace App\Http\UserInterfaces\SettingAttrDivs\Update;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\AttrDivUpdateFailureException;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputData;

class Presenter implements OutputBoundary
{
    private $presenterOutput;

    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     * @throws AttrDivUpdateFailureException
     */
    public function __invoke(OutputData $output)
    {
        if (!$output->result()) {
            throw new AttrDivUpdateFailureException();
        }

        return $this->presenterOutput->set(response(null, 204));
    }
}
