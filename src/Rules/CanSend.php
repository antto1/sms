<?php

namespace Antto\Sms\Rules;

use Antto\Sms\Facades\Sms;
use Illuminate\Contracts\Validation\Rule;

class CanSend implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Sms::canSend($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('sms::validation.can_send');
    }
}
