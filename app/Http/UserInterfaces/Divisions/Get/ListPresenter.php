<?php

namespace App\Http\UserInterfaces\Divisions\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\OutputData;

class ListPresenter implements OutputBoundary
{
    private $presenterOutput;

    /**
     * ListPresenter constructor.
     * @param PresenterOutput $presenterOutput
     */
    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        if (empty($output->divisions()) && empty($output->divisionMaps())) {
            abort(404);
        }

        $this->presenterOutput->set([
            'divisions' => $output->divisions(),
            'divisionMap' => $output->divisionMaps(),
        ]);
    }
}
