<?php

namespace App\Http\UserInterfaces\SearchConditions\Create;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputBoundary;

/**
 * Class Controller.
 */
final class Controller extends BaseController
{
    public function index(InputBoundary $inputBoundary, Request $request): void
    {
        $inputBoundary($request->inputData());
    }
}
