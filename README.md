# Laravel 短信/验证码扩展

## 安装

`composer require antto/sms`

```bash
# 发布配置
php artisan vendor:publish --tag=sms-config

# 发布迁移（*如需数据库记录发送日志）
php artisan vendor:publish --tag=sms-migrations
```

## 使用

### 示例

```php
<?php

namespace App\Http\Controllers;

use Antto\Sms\Facades\Sms;
use Illuminate\Http\Request;

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
            'mobile' => 'required|is_mobile|can_send',
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.is_mobile' => '手机号格式不正确',
            'mobile.can_send' => '验证码发送过于频繁',
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
            'mobile' => 'required|is_mobile',
            'code' => 'required|verify_code:' . $request->mobile,
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

### 表单验证

- is_mobile 验证手机号格式
- can_send 是否可发送，例如60秒内是否可重发
- verify_code 验证码是否正确，接受一个参数：手机号
