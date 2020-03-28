<?php

namespace Smart2\Application\Controllers;

use App\Http\UserInterfaces\CommercialAdvertising\Get\Request as AdvertisingRequest;
use App\Http\UserInterfaces\CommercialGrp\Get\Request as GrpRequest;
use App\Http\UserInterfaces\CommercialList\Get\Request as ListRequest;
use App\Http\UserInterfaces\ProgramList\Get\Request as ProgramListRequest;
use App\Http\UserInterfaces\ProgramPeriodAverage\Get\Request as ProgramPeriodAverageRequest;
use App\Http\UserInterfaces\ProgramTable\Get\Request as ProgramTableRequest;
use App\Http\UserInterfaces\RafChart\Get\Request as RafRequest;
use App\Http\UserInterfaces\RankingCommercial\Get\Request as RankingCommercialRequest;
use Illuminate\Http\JsonResponse;
use Smart2\Application\Exceptions\DateRangeException;
use Smart2\Application\Exceptions\SampleCountException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Requests\RatingPoints\IndexRequest;
use Smart2\UseCase\Analysis\AnalysisGetSearchConditionTextInteractor;
use Smart2\UseCase\Commercial\CommercialAdvertisingGetSearchConditionTextInteractor;
use Smart2\UseCase\Commercial\CommercialGrpSearchConditionTextInteractor;
use Smart2\UseCase\Commercial\CommercialListGetSearchConditionTextInteractor;
use Smart2\UseCase\Program\ProgramListGetSearchConditionInteractor;
use Smart2\UseCase\Program\ProgramPeriodAverageGetSearchConditionInteractor;
use Smart2\UseCase\Program\ProgramTableGetSearchConditionTextInteractor;
use Smart2\UseCase\Ranking\RankingCommercialGetSearchConditionTextInteractor;
use Smart2\UseCase\Rating\RatingGetSearchConditionTextInteractor;
use Smart2\UseCase\Rating\RatingInput;

class SearchConditionTextController extends Controller
{
    public function __construct()
    {
        $this->middleware('apitime');
    }

    /**
     * @param RatingGetSearchConditionTextInteractor $interactor
     * @param IndexRequest $request
     * @throws TrialException
     * @throws SampleCountException
     * @return JsonResponse
     */
    public function rating(RatingGetSearchConditionTextInteractor $interactor, IndexRequest $request): JsonResponse
    {
        $input = new RatingInput(
            $request->input('startDateTime'),
            $request->input('endDateTime'),
            $request->input('regionId'),
            $request->input('channels', []),
            $request->input('channelType'),
            $request->input('division'),
            $request->input('conditionCross'),
            $request->input('csvFlag'),
            $request->input('draw'),
            $request->input('code'),
            $request->input('dateRange'),
            $request->input('dataDivision'),
            $request->input('dataType'),
            $request->input('displayType'),
            $request->input('aggregateType'),
            $request->input('advertising'),
            $request->input('hour')
        );

        $data = $interactor->handle($input);

        return response()->json($data);
    }

    /**
     * 番組表.
     * @param ProgramTableGetSearchConditionTextInteractor $interactor
     * @param ProgramTableRequest $request
     * @throws TrialException
     * @return JsonResponse
     */
    public function programTable(ProgramTableGetSearchConditionTextInteractor $interactor, ProgramTableRequest $request): JsonResponse
    {
        $input = $request->inputData();

        $data = $interactor->handle($input);

        return response()->json($data);
    }

    /**
     * 番組リスト検索.
     * @param ProgramListGetSearchConditionInteractor $interactor
     * @param ProgramListRequest $request
     * @throws SampleCountException
     * @throws TrialException
     * @return JsonResponse
     */
    public function programList(ProgramListGetSearchConditionInteractor $interactor, ProgramListRequest $request): JsonResponse
    {
        $data = $interactor->handle($request->inputData());

        return response()->json($data);
    }

    /**
     * 番組期間平均.
     *
     * @param ProgramPeriodAverageGetSearchConditionInteractor $interactor
     * @param ProgramPeriodAverageRequest $request
     * @throws SampleCountException
     * @return JsonResponse
     */
    public function periodAverage(ProgramPeriodAverageGetSearchConditionInteractor $interactor, ProgramPeriodAverageRequest $request): JsonResponse
    {
        $input = $request->inputData();

        $data = $interactor->handle($input);

        return response()->json($data);
    }

    /**
     * CM GRP.
     *
     * @param CommercialGrpSearchConditionTextInteractor $interactor
     * @param $request
     * @throws SampleCountException
     * @throws TrialException
     * @return JsonResponse
     */
    public function grp(CommercialGrpSearchConditionTextInteractor $interactor, GrpRequest $request): JsonResponse
    {
        $data = $interactor->handle($request->inputData());

        return response()->json($data);
    }

    /**
     * CM Ranking.
     *
     * @param RankingCommercialGetSearchConditionTextInteractor $interactor
     * @param RankingCommercialRequest $request
     * @throws TrialException
     * @return JsonResponse
     */
    public function rankingCommercial(RankingCommercialGetSearchConditionTextInteractor $interactor, RankingCommercialRequest $request): JsonResponse
    {
        $data = $interactor->handle($request->inputData());

        return response()->json($data);
    }

    /**
     * CMリスト.
     *
     * @param CommercialListGetSearchConditionTextInteractor $interactor
     * @param ListRequest $request
     * @throws SampleCountException
     * @throws TrialException
     * @return JsonResponse
     */
    public function list(CommercialListGetSearchConditionTextInteractor $interactor, ListRequest $request): JsonResponse
    {
        $data = $interactor->handle($request->inputData());

        return response()->json($data);
    }

    /**
     * @param CommercialAdvertisingGetSearchConditionTextInteractor $interactor
     * @param AdvertisingRequest $request
     * @throws TrialException
     * @return JsonResponse
     */
    public function advertising(CommercialAdvertisingGetSearchConditionTextInteractor $interactor, AdvertisingRequest $request): JsonResponse
    {
        $input = $request->inputData();

        $data = $interactor->handle($input);

        return response()->json($data);
    }

    /**
     * R&F.
     *
     * @param AnalysisGetSearchConditionTextInteractor $interactor
     * @param HighchartRequest $request
     * @throws DateRangeException
     * @throws SampleCountException
     * @throws TrialException
     * @throws \Smart2\Application\Exceptions\AnalysisCsvProductAxisException
     * @return JsonResponse
     */
    // TODO - konno: 消滅させるときは analysis から raf に変更する
    public function analysis(AnalysisGetSearchConditionTextInteractor $interactor, RafRequest $request): JsonResponse
    {
        $input = $request->inputData();

        $data = $interactor->handle($input);

        return response()->json($data);
    }
}
