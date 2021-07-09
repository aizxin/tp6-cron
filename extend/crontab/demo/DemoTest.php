<?php
/**
 * FileName: DemoTest.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:49 下午
 */
declare (strict_types = 1);

namespace app\task\cron;


use crontab\Crontab;

class DemoTest extends Crontab
{
    protected $name = 'DemoTest';

    protected $rule = '*/5 * * * * *';

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return [static::class, 'run'];
    }


    public function run()
    {
        var_dump(1121212);
    }
}