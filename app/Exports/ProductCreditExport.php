<?php

namespace App\Exports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductCreditExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $categories_id;

    public function __construct($categories_id)
    {
        $this->categories_id = $categories_id;
    }

    public function view(): View
    {
        if ($this->categories_id)
            $products = Product::with('productLog')
                ->whereIn('category_id', $this->categories_id)
                ->orderBy('category_id')->get();
        else
            $products = Product::get();

        return view('exports.products', ['products' => $products]);
    }
}
