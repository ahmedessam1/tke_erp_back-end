<?php

namespace App\Exports;

use App\Models\Invoices\ImportInvoice;
use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;

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
        $invoices_ids = ImportInvoice::where('supplier_id', $supplier_id)->pluck('id');
        $products = ProductCredits::withProductAndImages()
            ->withImportInvoice()
            ->whereIn('import_invoice_id', $invoices_ids)
            ->select(DB::raw('SUM(quantity * purchase_price) / SUM(quantity) as average_purchase_price, sum(quantity) as quantity, product_id'))
            ->groupBy('product_id')
            ->get();

        return $products;
    }

    public function map($products): array {
        return [
            $products->product->name,
            $products->product->code,
            // ADDING WHITE SPACE AFTER BARCODE TO TREAT IT AS STRING IN EXCEL
            $products->product->barcode." ",
            $products->quantity,
            round($products->average_purchase_price, 3),
        ];
    }

    public function headings(): array {
        return [
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.QUANTITY'),
            trans('reports.CREDITS.EXCEL_COLUMNS.PURCHASE_PRICE'),
        ];
    }
}
