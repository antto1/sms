<?php

namespace Antto\Sms\Rules;

use Antto\Sms\Facades\Sms;
use Illuminate\Contracts\Validation\Rule;

class VerifyCode implements Rule
{
    protected $mobile;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mobile = null)
    {
        $this->mobile = $mobile;
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
        return Sms::verifyCode($this->mobile, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('sms::validation.sms_code_error');
    }
}
