<?php
/**
 * FileName: CrontabDispatcherProcess.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:42 下午
 */
declare (strict_types = 1);

namespace crontab\process;


use crontab\CoroutineStrategy;
use crontab\CrontabRegister;
use crontab\Scheduler;
use Swoole\Timer;
use Swoole\Server;
use Swoole\Process;
use think\App;
use think\facade\Log;

class CrontabDispatcherProcess
{

    /**
     * @var Server
     */
    private $server;

    /**
     * @var CrontabRegister
     */
    private $crontabRegister;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    public function __construct(App $app)
    {
        $this->server = $app->get(Server::class);
        $this->crontabRegister = $app->make(CrontabRegister::class);
        $this->scheduler = $app->make(Scheduler::class);
        $this->strategy = $app->make(CoroutineStrategy::class);
    }

    public function handle(): void
    {

        $enable = $this->app->config->get('crontab.enable', false);

        if ( ! $enable) return;

        $this->crontabRegister->handle();
        $process = new Process(function () {
            try {
                while (true) {
                    $this->sleep();
                    $crontabs = $this->scheduler->schedule();
                    while ( ! $crontabs->isEmpty()) {
                        $crontab = $crontabs->dequeue();
                        $this->strategy->dispatch($crontab);
                    }
                }
            } catch (\Throwable $throwable) {
                Log::error($throwable->getMessage());
            } finally {
                Timer::clearAll();
                sleep(5);
            }
        }, false, 0, true);

        $this->server->addProcess($process);
    }

    private function sleep()
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        Log::debug('Crontab dispatcher sleep ' . $sleep . 's.');
        $sleep > 0 && \Swoole\Coroutine::sleep($sleep);
    }
}