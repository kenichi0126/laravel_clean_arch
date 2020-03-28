<?php

namespace App\Http\UserInterfaces\UserNotice\Create;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\InputBoundary;

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
