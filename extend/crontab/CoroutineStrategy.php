<?php
/**
 * FileName: CoroutineStrategy.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:35 下午
 */
declare (strict_types = 1);

namespace crontab;


use Carbon\Carbon;
use Swoole\Coroutine;
use think\App;

class CoroutineStrategy
{
    /**
     * @var App
     */
    protected $app;

    /**
     * AbstractStrategy constructor.
     * @param $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function dispatch(Crontab $crontab)
    {
        Coroutine::create(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && Coroutine::sleep($wait);
                $executor = $this->app->make(Executor::class);
                $executor->execute($crontab);
            }
        });
    }
}