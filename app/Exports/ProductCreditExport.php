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
    private $categories_id, $filtering_options;

    public function __construct($categories_id, $filtering_options)
    {
        $this->categories_id = $categories_id;
        $this->filtering_options = $filtering_options;
    }

    public function view(): View
    {
        if(!$this->filtering_options)
            $this->filtering_options = [];

        if ($this->categories_id)
            $products = Product::with('productLog')
                ->whereIn('category_id', $this->categories_id)
                ->orderBy('category_id');
        else
            $products = Product::with('productLog');

        if(in_array('available_quantity', $this->filtering_options))
            $products = $products->whereHas('productLog', function($q) {
                $q->where('available_quantity', '>', '0');
            });

        $products = $products->get();

        return view('exports.products', ['products' => $products, 'filtering_options' => $this->filtering_options]);
    }
}
