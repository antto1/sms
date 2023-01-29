<?php

// config for antto/sms
return [
    'code' => [
        'template' => '', // 验证码模板
        'length' => 5, // 验证码长度
        'valid_minutes' => 10, // 验证码有效时间 分钟
        'with_minutes' => true, // 验证码模板是否带有有效期变量
        'interval' => 60, // 重发间隔 秒
    ],

    // 手机号正则
    'mobile_regex' => '/^((\+?86)|(\+86))?1\d{10}$/',

    'easy_sms' => [
        'timeout'  => 5.0,

        // 默认发送配置
        'default'  => [
            // 网关调用策略，默认：顺序调用
            'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

            // 默认可用的发送网关
            'gateways' => [
                'errorlog',
            ],
        ],

        // 可用的网关配置
        'gateways' => [
            'errorlog' => [
                'file' => storage_path('logs/sms.log'),
            ],
        ],
    ],

    // 发送记录写入数据库
    'dblog' => [
        'enable' => env('SMS_DBLOG', false),
        'table' => 'sms_log',
    ],
];
