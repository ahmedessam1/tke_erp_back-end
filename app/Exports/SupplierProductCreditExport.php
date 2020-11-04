<?php

namespace App\Exports;

use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierProductCreditExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $supplier_id;
    public function __construct($supplier_id) {
        $this->supplier_id = $supplier_id;
    }

    public function collection()
    {
        $supplier_id = $this->supplier_id;
        $product_credits = ProductCredits::whereHas('importInvoice', function($q) use ($supplier_id) {
            $q->where('supplier_id', $supplier_id);
        })->get();
        $product_unique_ids = [];
        foreach($product_credits as $p_c) {
            if (!in_array($p_c->product_id, $product_unique_ids))
                array_push($product_unique_ids, $p_c->product_id);
        }

        $products = Product::whereIn('id', $product_unique_ids)
            ->whereHas('productLog', function ($q) {
                $q->where('available_quantity', '>', 0);
            })
            ->get();

        return $products;
    }

    public function map($products): array {
        return [
            $products->id,
            $products->name,
            $products->code,
            // ADDING WHITE SPACE AFTER BARCODE TO TREAT IT AS STRING IN EXCEL
            $products->barcode." ",
            $products->report_total_quantity,
        ];
    }

    public function headings(): array {
        return [
            trans('reports.CREDITS.EXCEL_COLUMNS.ID'),
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.TOTAL_QUANTITY'),
        ];
    }
}
