<?php

namespace Switchm\SmartApi\Components\TopRanking\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Queries\Dao\Dwh\TopDao;

class Interactor implements InputBoundary
{
    private $dwhTopDao;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * TopGetRankingInteractor constructor.
     * @param TopDao $dwhTopDao
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        TopDao $dwhTopDao,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->dwhTopDao = $dwhTopDao;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $regionId = $inputData->regionId();
        $channelIds = [];
        $conv15SecFlag = $inputData->conv15SecFlag();
        $broadcasterCompanyIds = $inputData->broadcasterCompanyIds();

        if ($regionId === 1) {
            $channelIds = [1, 3, 4, 5, 6, 7];
        } elseif ($regionId === 2) {
            $channelIds = [44, 46, 47, 48, 49, 50];
        }

        $data = $this->getRankings($regionId, $channelIds, $conv15SecFlag, $broadcasterCompanyIds);
        $output = new OutputData(
            $data['program'],
            $data['company_cm'],
            $data['product_cm'],
            $data['programDate'],
            $data['cmDate'],
            $data['programPhNumbers'],
            $data['cmPhNumbers']
        );

        ($this->outputBoundary)($output);
    }

    /**
     * @param int $regionId
     * @param array $channelIds
     * @param int $conv15SecFlag
     * @param array $broadcasterCompanyIds
     * @return array
     */
    private function getRankings(int $regionId, array $channelIds, int $conv15SecFlag, array $broadcasterCompanyIds): array
    {
        $now = Carbon::now();
        $from = Carbon::now()->subMonthNoOverflow(1)->startOfMonth()->hour(5)->minute(0)->second(0);
        $to = Carbon::now()->subMonthNoOverflow(1)->endOfMonth()->addDay(1)->hour(4)->minute(59)->second(59);

        if ($now->day <= 3) {
            $from->subMonthNoOverflow(1);
            $to->subMonthNoOverflow(1);
        }

        $lastWeekFrom = Carbon::now()->subWeek(1)->startOfWeek()->hour(5)->minute(0)->second(0);
        $lastWeekTo = Carbon::now()->subWeek(1)->endOfWeek()->addDay(1)->hour(4)->minute(59)->second(59);
        $programRanks = $this->dwhTopDao->findProgramRanking($lastWeekFrom, $lastWeekTo, $channelIds);
        $programDate = $lastWeekFrom->format('Y年m月d日') . '～' . $lastWeekTo->subDay(1)->format('Y年m月d日');

        $programPhNumbers = $this->searchConditionTextAppService->getPersonalHouseholdNumbers($lastWeekFrom, $lastWeekTo, '', '', $regionId);

        foreach ($programPhNumbers as &$num) {
            $num = number_format($num);
        }

        $companyCmRanks = $this->dwhTopDao->findCmRankingOfCompany($from, $to, $regionId, $channelIds, $conv15SecFlag, $broadcasterCompanyIds);
        $productCmRanks = $this->dwhTopDao->findCmRankingOfProduct($from, $to, $regionId, $channelIds, $broadcasterCompanyIds);

        foreach ($productCmRanks as &$productCmRank) {
            $work = explode('/', $productCmRank['company_name']);
            $productCmRank['company_name'] = $work[0];
        }

        $cmPhNumbers = $this->searchConditionTextAppService->getPersonalHouseholdNumbers($to, $to, '', '', $regionId);

        foreach ($cmPhNumbers as &$num) {
            $num = number_format($num);
        }

        return ['program' => $programRanks, 'company_cm' => $companyCmRanks, 'product_cm' => $productCmRanks, 'programDate' => $programDate, 'cmDate' => $from->format('Y年m月'), 'programPhNumbers' => $programPhNumbers, 'cmPhNumbers' => $cmPhNumbers];
    }
}
