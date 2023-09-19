<?php

namespace Antto\Sms\Gateways;

use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Traits\HasHttpRequest;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;

class YunXinShiGateway extends Gateway
{
    use HasHttpRequest;

    /**
     * @param \Overtrue\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Overtrue\EasySms\Contracts\MessageInterface     $message
     * @param \Overtrue\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \Overtrue\EasySms\Exceptions\GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'ac'       => 'send',
            'uid'      => $config->get('uid'),
            'pwd'      => $config->get('pwd'),
            'template' => $message->getTemplate($this),
            'mobile'   => $to->getNumber(),
            'content'  => $this->buildContent($message)
        ];

        $result = $this->get($config->get('apiUrl'), $params);
        $result = json_decode($result, true);

        if ($result['stat'] != '100') {
            throw new GatewayErrorException($result['message'], $result['stat'], $result);
        }

        return $result;
    }

    /**
     * 构建发送内容
     * 优先使用 content，如果 content 没有内容，用 data 数据合成内容，或者直接使用 data 的值
     *
     * @param MessageInterface $message
     * @return string
     */
    protected function buildContent(MessageInterface $message)
    {
        $content = $message->getContent($this); // 获取短信内容
        $data = $message->getData($this); // 获取短信模板变量

        if ($content) {
            return $content;
        }

        if (is_array($data)) {
            return json_encode($data);
        }

        return $data;
    }
}
