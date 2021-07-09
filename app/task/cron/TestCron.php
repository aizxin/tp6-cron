<?php
/**
 * FileName: TestCron.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/2 10:44 上午
 */
declare (strict_types = 1);

namespace app\task\cron;


use yunwuxin\cron\Task;

class TestCron extends Task
{
    protected function configure()
    {
        return $this->everyMinute(); //设置任务的周期，每天执行一次，更多的方法可以查看源代码，都有注释
    }

    /**
     * 执行任务
     * @return mixed
     */
    protected function execute()
    {
        var_dump(1111111);
    }
}