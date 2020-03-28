<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputBoundary;

final class Controller extends BaseController
{
    /**
     * index.
     *
     * @param InputBoundary $inputBoundary
     * @param Request $request
     */
    public function index(InputBoundary $inputBoundary, Request $request): void
    {
        $inputBoundary($request->inputData());
    }
}
