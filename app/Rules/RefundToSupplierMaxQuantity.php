<?php

namespace App\Rules;

use App\Models\Product\Product;
use App\Models\Refund\Refund;
use Illuminate\Contracts\Validation\Rule;

class RefundToSupplierMaxQuantity implements Rule
{
    private $order_id, $product_id, $quantity;

    public function __construct($order_id, $product_id, $quantity)
    {
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }

    public function passes($attribute, $value)
    {
        $order = Refund::find($this->order_id);
        if ($order)
            $refund_type = $order->type;
        else return false;
        if ($refund_type === 'out') {
            $product_quantity = Product::find($this->product_id)->report_total_quantity;
            if ($this->quantity > $product_quantity)
                return false;
            else
                return true;
        }else return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.refunds_validation.products.quantity.max');
    }
}
