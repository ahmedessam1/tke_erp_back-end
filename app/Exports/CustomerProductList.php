<?php

namespace App\Exports;

use App\Models\Customer\CustomerPriceList;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerProductList implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $customer_id;

    public function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CustomerPriceList::all();
    }

    public function map($products): array
    {
        return [
            $products->product_barcode . " ",
            $products->product_name,
            $products->product_selling_price,
        ];
    }

    public function headings(): array
    {
        return [
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.PRICE')
        ];
    }
}
