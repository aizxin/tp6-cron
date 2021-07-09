<?php
/**
 * FileName: crontab.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 10:54 上午
 */
declare (strict_types = 1);


return [
    'crontab' => [
        app(\app\task\cron\TestCrontab::class),
    ],
];