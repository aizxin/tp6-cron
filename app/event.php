<?php
// 事件定义文件
return [
    'bind' => [
    ],

    'listen' => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],

        'swoole.init' => [
//            \ThinkSwooleCrontab\Process\CrontabDispatcherProcess::class,
            \cron\Process::class,
        ],
    ],

    'subscribe' => [
    ],
];
