<?php

namespace App\Repositories\Reports\Contracts;

interface ReportSalesRepository {
    // GET THE CUSTOMER BRANCH SALES REPORT
    /**
     * @param $request
     * @return mixed
     * Description:
     * This report takes multiple customer branches and a year to
     * make a sales comparison between them within the selected year's months
     */
    public function customerBranchSalesCompare($request);

    // GET THE CUSTOMER BRANCH PRODUCTS WITHDRAWALS REPORT
    /**
     * @param $request
     * @return mixed
     * Description:
     * This report takes customer branch, from date, to date and products category (Optional) to
     * get the products that this customer branch requested in this time range
     * and also to get invoices within this time range
     */
    public function customerBranchProductsWithdrawals($request);

    // GET YEARLY SALES
    /**
     * @param $years
     * @param $filters
     * @return mixed
     * Description:
     * This report take multiple years and filters (Sales or Refunds) to
     * get each month related to each year sales for each filter
     */
    public function yearlySales($years, $filters);

    // GET CUSTOMER SALES AND REFUNDS
    /**
     * @param $customer_id
     * @param $year
     * @return mixed
     * Description:
     * This report take the customer ID and a year to
     * get all the customer's branches net sales, refunds and sales without refunds
     */
    public function customerSalesAndRefunds($customer_id, $year);

    // GET CUSTOMERS STATEMENT
    /**
     * @param $customers_id
     * @param $from_date
     * @param $to_date
     * @return mixed
     * Description:
     * This report take multiple customers by IDs and a time range to
     * generate downloadable Excel sheet containing:
     * All the customer's branches, invoices number, invoices date, invoices total
     */
    public function customersStatement($customers_id, $from_date, $to_date);

    /**
     * @param $year
     * @param $seller_id
     * @return mixed
     */
    public function sellersProgress($year, $seller_id);
}
