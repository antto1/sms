<?php

namespace Antto\Sms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Antto\Sms\Sms
 */
class Sms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}
