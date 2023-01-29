<?php

namespace Antto\Sms\Rules;

use Antto\Sms\Facades\Sms;
use Illuminate\Contracts\Validation\Rule;

class IsMobile implements Rule
{

    protected $regex;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($regex = null)
    {
        $this->regex = $regex ?: config('sms.mobile_regex', '/^((\+?86)|(\+86))?1\d{10}$/');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Sms::verifyMobile($value, $this->regex);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('sms::validation.mobile_error');
    }
}
