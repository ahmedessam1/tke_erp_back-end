<?php

namespace App\Repositories\Reports\Contracts;

interface ReportProductRepository {
    // REPORT EXPORT EXCEL THAT CONTAIN THE PRODUCT CREDIT
    public function exportProductsCredit($request);

    // RETURN THE PRODUCT HISTORY WITHIN A PERIOD OF TIME
    public function productHistory($request);

    // GET TOP SOLD PRODUCTS BY AMOUNT PER YEAR
    public function topSoldByAmount($year);

    // GET TOP SOLD PRODUCTS REPEATEDLY
    public function topSoldRepeatedly($year);

    // GET TOP SOLD PRODUCTS PROFIT (HIGHEST AND LOWEST)
    public function topSoldProfit($type);

    // REPORT EXPORT EXCEL THAT CONTAIN THE SUPPLIER PRODUCTS CREDIT
    public function exportSupplierProductsCredit($request);

    // PRODUCTS SALES BY CATEGORY WITHIN TIME RANGE
    public function sales($request);
}
