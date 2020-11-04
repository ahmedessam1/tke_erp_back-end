<?php

namespace App\Rules\ExportInvoices;

use App\Models\Customer\CustomerBranch;
use App\Models\Customer\CustomerBranchesSellers;
use Illuminate\Contracts\Validation\Rule;

class SellerMatchBranch implements Rule
{
    private $branch_id, $seller_id;

    public function __construct($branch_id, $seller_id)
    {
        $this -> branch_id = $branch_id;
        $this -> seller_id = $seller_id;
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
        return CustomerBranchesSellers::where('customer_branch_id', $this -> branch_id)
            -> where('seller_id', $this -> seller_id)
            -> exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.export_invoices_validation.seller_id.exists');
    }
}
