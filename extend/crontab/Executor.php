<?php
/**
 * FileName: Executor.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:36 下午
 */
declare (strict_types = 1);

namespace crontab;


use Swoole\Coroutine;
use Swoole\Timer;
use think\App;
use think\Log;
use Carbon\Carbon;

class Executor
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Log
     */
    private $logger;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->logger = $app->log;
    }

    public function execute(Crontab $crontab)
    {
        if (! $crontab instanceof Crontab || ! $crontab->getExecuteTime()) {
            return;
        }
        $diff = $crontab->getExecuteTime()->diffInRealSeconds(new Carbon());
        $callback = null;
        switch ($crontab->getType()) {
            case 'callback':
                [$class, $method] = $crontab->getCallback();
                $parameters = $crontab->getCallback()[2] ?? null;
                if ($class && $method && method_exists($class, $method)) {
                    $callback = function () use ($class, $method, $parameters, $crontab) {
                        $runnable = function () use ($class, $method, $parameters, $crontab) {
                            try {
                                $result = true;
                                $instance = $this->app->make($class);
                                if ($parameters && is_array($parameters)) {
                                    $instance->{$method}(...$parameters);
                                } else {
                                    $instance->{$method}();
                                }
                            } catch (\Throwable $throwable) {
                                $result = false;
                            } finally {
                                $this->logResult($crontab, $result);
                            }
                        };

                        Coroutine::create($runnable);
                    };
                }
                break;
        }
        $callback && Timer::after($diff > 0 ? $diff * 1000 : 1, $callback);
    }

    protected function logResult(Crontab $crontab, bool $isSuccess)
    {
        if ($isSuccess) {
            $this->logger->info(sprintf('Crontab task [%s] executed successfully at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        } else {
            $this->logger->error(sprintf('Crontab task [%s] failed execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        }
    }
}