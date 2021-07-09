<?php
/**
 * FileName: crontab.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:53 下午
 */
declare (strict_types = 1);

return [
    'enable'  => true,
    'crontab' => [
        \app\task\cron\DemoTest::class,
    ],
];