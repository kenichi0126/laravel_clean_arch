<?php

namespace App\Http\UserInterfaces\SearchConditions\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputBoundary;

/**
 * Class Controller.
 */
final class Controller extends BaseController
{
    /**
     * @param InputBoundary $inputBoundary
     * @param Request $request
     */
    public function index(Inputboundary $inputBoundary, Request $request): void
    {
        $inputBoundary($request->inputData());
    }
}
