<?php

namespace App\Http\UserInterfaces\ProgramLatestDate\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\InputBoundary;

class Controller extends BaseController
{
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
        $inputBoundary();
    }
}
