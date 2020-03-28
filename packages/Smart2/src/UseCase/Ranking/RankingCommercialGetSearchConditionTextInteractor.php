<?php

namespace Smart2\UseCase\Ranking;

use Illuminate\Auth\AuthenticationException;
use Smart2\Application\Exceptions\TrialException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Smart2\Application\Services\UserInfoService;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputData;
use Switchm\SmartApi\Queries\Dao\Dwh\RankingDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;
use Switchm\SmartApi\Queries\Services\DivisionService;

class RankingCommercialGetSearchConditionTextInteractor
{
    private $rankingDao;

    private $mdataCmGenreDao;

    private $divisionService;

    private $userInfoService;

    private $searchConditionTextAppService;

    /**
     * RankingCommercialGetSearchConditionTextInteractor constructor.
     * @param RankingDao $rankingDao
     * @param MdataCmGenreDao $mdataCmGenreDao
     * @param DivisionService $divisionService
     * @param UserInfoService $userInfoService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @throws AuthenticationException
     */
    public function __construct(RankingDao $rankingDao, MdataCmGenreDao $mdataCmGenreDao, DivisionService $divisionService, UserInfoService $userInfoService, SearchConditionTextAppService $searchConditionTextAppService)
    {
        $this->rankingDao = $rankingDao;
        $this->mdataCmGenreDao = $mdataCmGenreDao;
        $this->divisionService = $divisionService;
        $this->userInfoService = $userInfoService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $input
     * @throws TrialException
     * @return array
     */
    public function handle(InputData $input): array
    {
        $this->isDuringTrial($input->startDateTime(), $input->endDateTime());

        $cnt = 0;
        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), \Auth::id(), \Config::get('const.BASE_DIVISION'));

        $params = $this->getParams($input);

        $header = $this->searchConditionTextAppService->getRankingCommercialHeader(...array_merge($params, [
            $codeList, $cnt,
        ]));

        return $header;
    }

    /**
     * @param string $startDateTime
     * @param string $endDateTime
     * @throws TrialException
     */
    protected function isDuringTrial(string $startDateTime, string $endDateTime): void
    {
        if (!\Auth::getUser()->isDuringTrial($startDateTime, $endDateTime)) {
            throw new TrialException();
        }
    }

    /**
     * @param InputData $input
     * @return array
     */
    protected function getParams(InputData $input): array
    {
        return [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->wdays(),
            $input->isHoliday(),
            $input->cmType(),
            $input->regionId(),
            $input->division(),
            $input->codes(),
            $input->conditionCross(),
            $input->channels(),
            $input->order(),
            $input->conv15SecFlag(),
            $input->period(),
            $input->straddlingFlg(),
            $input->dispCount(),
            $input->page(),
            $input->csvFlag(),
            $input->dataType(),
            $input->cmLargeGenres(),
            $input->axisType(),
        ];
    }
}
