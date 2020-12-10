<?php

namespace App\Models\Refund;

use App\Models\Product\Product;
use App\Observers\RefundProductObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundProduct extends Model
{
    use SoftDeletes;

    // FILLABLE
    protected $fillable = [
        'refund_id', 'product_id', 'quantity', 'price', 'discount', 'valid'
    ];

    // APPENDS
    protected $appends = ['item_net_price'];

    // DATES
    protected $dates = ['deleted_at'];

    // REGISTER OBSERVER
    public static function boot()
    {
        parent::boot();
        RefundProduct::observe(new RefundProductObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function refundOrder()
    {
        return $this->belongsTo('App\Models\Refund\Refund', 'refund_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product')->with('category')->withTrashed();
    }

    // SINGLE ITEM NET PRICE
    public function getItemNetPriceAttribute()
    {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $refund_price = $this->price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->refundOrder->tax;
        $invoice_discount = $this->refundOrder->discount;

        $quantity = 1;
        $company_tax = false;
        $value = $this->calculateNetPrice($refund_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['item_net_price'] = $value;
    }

    private function calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax)
    {
        // CALCULATE DISCOUNT
        $sold_price = $sold_price * $quantity;
        $discount_value = $sold_price * $discount / 100;
        $net_price = $sold_price - $discount_value;

        // CALCULATE INVOICE DISCOUNT
        $invoice_discount_value = $net_price * $invoice_discount / 100;
        $net_price -= $invoice_discount_value;

        // CALCULATE TAX
        if ($tax)
            $net_price *= config('constants.tax');

        if ($company_tax)
            $net_price *= config('constants.tax');
        return $net_price;
    }

    // SCOPES
    public function scopeWithProduct($builder)
    {
        return $builder->with('product');
    }
}
