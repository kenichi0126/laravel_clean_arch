<?php

namespace App\Http\UserInterfaces\Setting\Save;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputBoundary;

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
