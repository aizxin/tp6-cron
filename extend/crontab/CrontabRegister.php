<?php
/**
 * FileName: CrontabRegister.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:26 下午
 */
declare (strict_types = 1);

namespace crontab;

use think\App;
use Closure;

class CrontabRegister
{
    /**
     * @var CrontabManager
     */
    private $crontabManager;

    /**
     * @var App
     */
    private $app;


    public function __construct(CrontabManager $crontabManager, App $app)
    {
        $this->crontabManager = $crontabManager;
        $this->app = $app;
    }


    public function handle(): void
    {
        $crontabs = $this->parseCrontabs();
        foreach ($crontabs as $crontab) {
            if ($crontab instanceof Closure) {
                $instances = $crontab;
            } else {
                $instances = $this->app->make($crontab);
            }
            if ($instances instanceof Crontab) {
                $this->crontabManager->register($instances);
            }
        }
    }

    private function parseCrontabs(): array
    {
        return $this->app->config->get('crontab.crontab', []);
    }
}