<?php

namespace Antto\Sms;

use Antto\Sms\Facades\Sms;

class Validator
{
    function isMobile($attribute, $value, $parameters, $validator)
    {
        return Sms::verifyMobile($value);
    }

    function canSend($attribute, $value, $parameters, $validator)
    {
        return Sms::canSend($value);
    }

    function verifyCode($attribute, $value, $parameters, $validator)
    {
        return Sms::verifyCode($parameters[0], $value);
    }
}
