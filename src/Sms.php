<?php

namespace Antto\Sms;

use Illuminate\Support\Facades\Schema;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Sms
{
    private $easySms;

    public function __construct()
    {
        $this->easySms = new \Overtrue\EasySms\EasySms(config('sms.easy_sms'));
    }

    /**
     * 发送短信
     *
     * @param string|array                                       $to 手机号码
     * @param \Overtrue\EasySms\Contracts\MessageInterface|array $message
     * @param array                                              $gateways
     *
     * @return void
     */
    public function send($to, $message, array $gateways = [])
    {
        $flag = false;
        try {
            $results = $this->easySms->send($to, $message, $gateways);

            foreach ($results as $value) {
                if ('success' == $value['status']) {
                    $flag = true;
                }
            }
        } catch (NoGatewayAvailableException $noGatewayAvailableException) {
            $results = $noGatewayAvailableException->results;
            $flag = false;
        } catch (\Exception $exception) {
            $results = $exception->getMessage();
            $flag = false;
        }

        // 记录短信日志
        if (config('sms.dblog.enable') && Schema::hasTable(config('sms.dblog.table'))) {
            try {
                \DB::table('sms_log')->insert([
                    'mobile' => $to,
                    'data' => json_encode($message),
                    'is_sent' => $flag,
                    'result' => json_encode($results),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $th) {
                report($th);
            }
        }

        return $flag;
    }

    /**
     * 发送短信验证码
     *
     * @param string $to 手机号码
     *
     * @return false|string 验证码
     */
    public function sendVerifyCode($to)
    {
        $code = $this->generateCode($to, config('sms.code.length'));

        $message = [
            'template' => config('sms.code.template'),
            'data' => [
                'code' => $code,
            ],
        ];

        if (config('sms.code.with_minutes')) {
            // 加入 验证码有效时间 变量
            $message['data']['minutes'] = config('sms.code.valid_minutes', 10);
        }

        $result = $this->send($to, $message);

        if ($result) {
            $this->cacheCode($to, $code);
            return $code;
        } else {
            return false;
        }
    }

    /**
     * 缓存验证码
     *
     * @param string $to 手机号
     * @param string $code 验证码
     * @param int $minutes 有效时间 分钟
     * @param int $sent_at 发送时间 时间戳
     */
    public function cacheCode($to, $code, $minutes = null, $sent_at = null)
    {
        $sent_at = $sent_at ?: time();
        $valid_minutes = $minutes ?: config('sms.code.valid_minutes', 10);

        cache()->put(
            $this->getKey($to),
            [
                'code' => $code,
                'sent_at' => time(),
            ],
            $valid_minutes * 60
        );
    }

    /**
     * 获取缓存 key
     *
     * @param string $to 手机号
     *
     * @return string
     */
    public function getKey($to)
    {
        return md5('sms_code_' . $to);
    }

    /**
     * 生成验证码
     *
     * @param string $to 手机号
     * @param int $length 验证码长度
     *
     * @return string
     */
    public function generateCode($to, $length = 5)
    {
        $characters   = '0123456789';
        $charLength   = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charLength - 1)];
        }

        return $randomString;
    }

    /**
     * 检查验证码是否可发送
     *
     * @param $to 手机号
     *
     * @return bool
     */
    public function canSend($to)
    {
        $code = cache()->get($this->getKey($to), '');
        $sent_at = $code['sent_at'] ?? 0;

        if (empty($code) || time() - $sent_at > config('sms.code.interval', 60)) {
            return true;
        }

        return false;
    }

    /**
     * 验证手机号
     *
     * @param $to 手机号
     *
     * @return false|int
     */
    public function verifyMobile($to, $regex = null)
    {
        $regex = $regex ?: config('sms.mobile_regex', '/^((\+?86)|(\+86))?1\d{10}$/');

        return preg_match($regex, $to) ? true : false;
    }

    /**
     * 检查验证码
     *
     * @param $to 手机号
     * @param $code 验证码
     *
     * @return bool
     */
    public function verifyCode($to, $code)
    {
        $key = $this->getKey($to);

        $code = cache()->get($key, '');

        if (empty($code)) {
            return false;
        }

        if (isset($code['code']) && $code['code'] == $code) {
            cache()->forget($key);

            return true;
        }

        return false;
    }
}
