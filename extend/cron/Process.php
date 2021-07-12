<?php
/**
 * FileName: Process.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/2 10:31 上午
 */
declare (strict_types = 1);

namespace cron;


use Carbon\Carbon;
use Swoole\Server;
use think\App;
use think\facade\Log;
use yunwuxin\cron\Task;

class Process
{
    protected $app;
    protected $schedules;
    protected $server;
    protected $startedAt;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->schedules = new \SplQueue();
        $this->server = $app->get(Server::class);
        $this->startedAt = Carbon::now();
    }


    public function handle(): void
    {
        $process = new \Swoole\Process(function () {
            try {
                while (true) {
                    $this->sleep();
                    $crontabs = $this->schedule();
                    while ( ! $crontabs->isEmpty()) {
                        /**
                         * @var Task $task
                         */
                        $task = $crontabs->dequeue();

                        if ($task->isDue()) {

                            if ( ! $task->filtersPass()) {
                                continue;
                            }

                            if ($task->onOneServer) {
                                $this->runSingleServerTask($task);
                            } else {
                                $this->runTask($task);
                            }
                            $this->app->log->write("Task " . get_class($task) . " run at " . Carbon::now());
                        }
                    }
                }
            } catch (\Throwable $throwable) {
                Log::error($throwable->getMessage());
            } finally {
                sleep(5);
            }
        }, false, 0, true);

        $this->server->addProcess($process);
    }

    private function sleep()
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        Log::debug('Task run sleep ' . $sleep . 's.');
        $sleep > 0 && \Swoole\Coroutine::sleep($sleep);
    }

    protected function schedule(): \SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }

        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        $tasks = $this->app->config->get('cron.tasks');

        $taskData = [];
        foreach ($tasks as $taskClass) {
            $taskData[] = $this->app->make($taskClass);
        }

        return $taskData;
    }

    /**
     * @param $task Task
     *
     * @return bool
     */
    protected function serverShouldRun($task)
    {
        $key = $task->mutexName() . $this->startedAt->format('Hi');
        if ($this->app->cache->has($key)) {
            return false;
        }
        $this->app->cache->set($key, true, 60);

        return true;
    }

    protected function runSingleServerTask($task)
    {
        if ($this->serverShouldRun($task)) {
            $this->runTask($task);
        } else {
            $this->app->log->write('Skipping task (has already run on another server):' . get_class($task));
        }
    }

    /**
     * @param $task Task
     */
    protected function runTask($task)
    {
        $task->run();
    }
}