<?php

namespace Switchm\SmartApi\Components\UserNotice\Create\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Queries\Dao\Rdb\NoticeDao;

class Interactor implements InputBoundary
{
    private $noticeDao;

    private $dataAccess;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param NoticeDao $noticeDao
     * @param DataAccessInterface $dataAccess
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(NoticeDao $noticeDao, DataAccessInterface $dataAccess, OutputBoundary $outputBoundary)
    {
        $this->noticeDao = $noticeDao;
        $this->dataAccess = $dataAccess;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $input
     * @return mixed|void
     */
    public function __invoke(InputData $input): void
    {
        $userNoticesRead = $this->noticeDao->searchUserNoticeRead($input->noticeId(), $input->memberId());

        if (count($userNoticesRead) === 0) {
            ($this->dataAccess)($input->noticeId(), $input->memberId(), Carbon::now()->format('Y-m-d H:i:s'));
        }
        $outputData = new OutputData();
        ($this->outputBoundary)($outputData);
    }
}
