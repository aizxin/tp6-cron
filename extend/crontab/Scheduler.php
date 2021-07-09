<?php
/**
 * FileName: Scheduler.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:25 下午
 */
declare (strict_types = 1);

namespace crontab;


class Scheduler
{
    /**
     * @var CrontabManager
     */
    protected $crontabManager;

    /**
     * @var \SplQueue
     */
    protected $schedules;

    public function __construct(CrontabManager $crontabManager)
    {
        $this->schedules = new \SplQueue();
        $this->crontabManager = $crontabManager;
    }

    public function schedule(): \SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }
        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        return $this->crontabManager->parse();
    }
}
