<?php

namespace App\Http\UserInterfaces\SettingAttrDivsOrder\Update;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\AttrDivUpdateFailureException;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\OutputData;

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
        return $this->presenterOutput->set(response(null, 204));
    }
}
