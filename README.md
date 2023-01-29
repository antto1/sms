# Laravel 短信/验证码扩展

## 安装

`composer require antto/sms`

```bash
# 发布配置
php artisan vendor:publish --tag=sms-config

# 发布迁移（*如需数据库记录发送日志）
php artisan vendor:publish --tag=sms-migrations

# 发布翻译
php artisan vendor:publish --tag=sms-lang
```

## 使用

```php
<?php

namespace App\Http\Controllers;

use Antto\Sms\Facades\Sms;
use Antto\Sms\Rules\CanSend;
use Illuminate\Http\Request;
use Antto\Sms\Rules\IsMobile;
use Antto\Sms\Rules\VerifyCode;

class TestController extends Controller
{
    /**
     * 发送短信
     */
    public function sendSms(Request $request)
    {
        // 更多用法参考 easy_sms
        $result = Sms::send('17098764321', [
            'template' => 'SMS_001',
            'data' => [
                'name' => 'test'
            ],
        ]);
    }

    /**
     * 发送验证码
     */
    public function sendVerifyCode(Request $request)
    {
        $request->validate([
            'mobile' => ['required', new IsMobile, new CanSend],
        ]);

        if (Sms::sendVerifyCode($request->mobile)) {
            return response()->json([
                'message' => '发送成功',
            ]);
        } else {
            return response()->json([
                'message' => '发送失败',
            ], 500);
        }
    }

    /**
     * 验证验证码
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'mobile' => ['required', new IsMobile],
            'code' => ['required', new VerifyCode($request->mobile)],
        ]);

        // ...
        // ...
        // ...


        return response()->json([
            'message' => '操作成功',
        ]);
    }
}

```
