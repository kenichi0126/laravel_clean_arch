<?php

namespace App\Http\UserInterfaces\RatingPerMinutes\Get;

use Switchm\Php\Illuminate\Routing\Controller as BaseController;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\InputBoundary;

final class Controller extends BaseController
{
    public function __construct()
    {
        $this->middleware('apitime');
    }

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
