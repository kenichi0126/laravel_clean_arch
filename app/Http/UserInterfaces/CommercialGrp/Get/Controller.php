<?php

namespace App\Http\UserInterfaces\CommercialGrp\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputBoundary;

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
