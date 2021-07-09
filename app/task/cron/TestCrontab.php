<?php
/**
 * FileName: TestCrontab.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 10:55 上午
 */
declare (strict_types = 1);

namespace app\task\cron;


use ThinkSwooleCrontab\Crontab;


/**
 * Class TestCrontab
 * @package app\task\cron
 *          (new \ThinkSwooleCrontab\Crontab())->setName('test-1')
 * ->setRule('* * * * * *')
 * ->setCallback([Test::class, 'run'])
 * ->setMemo('just a test crontab'),
 */
class TestCrontab extends Crontab
{
    protected $name = 'TestCrontab';

    protected $rule = '*/5 * * * * *';

    protected $memo = 'just a test crontab';


    public function __construct()
    {
        $this->setCallback([static::class, 'run']);
    }

    public function run()
    {
        var_dump(1121212);
    }
}
