<?php

namespace App\Exports;

use App\Models\Product\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductCreditExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $categories_id;

    public function __construct($categories_id)
    {
        $this->categories_id = $categories_id;
    }

    public function collection()
    {
        if ($this->categories_id)
            $products = Product::whereIn('category_id', $this->categories_id)->has('credits')->orderBy('category_id')->get();
        else
            $products = Product::has('credits')->get();
        return $products;
    }

    public function map($products): array
    {
        return [
            $products->id,
            $products->name,
            $products->code,
            // ADDING WHITE SPACE AFTER BARCODE TO TREAT IT AS STRING IN EXCEL
            $products->barcode . " ",
            $products->category->name,
            $products->report_total_quantity,
            $products->report_avg_purchase_price,
            $products->report_total_credit,
        ];
    }

    public function headings(): array
    {
        $headers = [
            trans('reports.CREDITS.EXCEL_COLUMNS.ID'),
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CATEGORY'),
            trans('reports.CREDITS.EXCEL_COLUMNS.TOTAL_QUANTITY'),
        ];

        if (Auth::user()->hasRole(['super_admin'])) {
            array_push($headers, trans('reports.CREDITS.EXCEL_COLUMNS.AVG_PURCHASE_PRICE'));
            array_push($headers, trans('reports.CREDITS.EXCEL_COLUMNS.TOTAL_CREDIT'));
        }

        return $headers;
    }
}
