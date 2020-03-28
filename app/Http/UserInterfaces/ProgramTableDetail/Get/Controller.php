<?php

namespace App\Http\UserInterfaces\ProgramTableDetail\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputBoundary;

class Controller extends BaseController
{
    /**
     * SampleCountController constructor.
     */
    public function __construct()
    {
        $this->middleware('apitime');
    }

    /**
     * @param InputBoundary $inputBoundary
     * @param Request $request
     */
    public function index(InputBoundary $inputBoundary, Request $request): void
    {
        $inputBoundary($request->inputData());
    }
}
