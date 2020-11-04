<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ToDateMoreThanFromDateRule implements Rule
{
    private $from_date, $to_date;

    public function __construct($from_date, $to_date)
    {
        $this -> from_date = $from_date;
        $this -> to_date = $to_date;
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
        if ($this -> to_date < $this -> from_date)
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
        return trans('form_responses.general.to_date_more_than_from_date');
    }
}
