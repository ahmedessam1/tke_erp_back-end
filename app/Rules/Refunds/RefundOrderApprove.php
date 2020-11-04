<?php

namespace App\Rules\Refunds;

use App\Models\Refund\Refund;
use Illuminate\Contracts\Validation\Rule;

class RefundOrderApprove implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $refund_id;
    public function __construct($refund_id)
    {
        $this -> refund_id = $refund_id;
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
        $flag = Refund::notApproved() -> find($this -> refund_id);
        if (!$flag)
            return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.refunds_validation.products.refund_id.approved');
    }
}
