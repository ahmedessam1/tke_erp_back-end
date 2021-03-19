<?php

namespace App\Repositories\Contracts;

interface RequirementRepository {
    // RETURN USERS
    public function users();

    // RETURN PAYMENT TYPES
    public function paymentTypes();

    // RETURN SUPPLIERS
    public function suppliers();

    // RETURN JOB POSITIONS
    public function positions();

    // RETURN CATEGORIES
    public function categories();

    // RETURN CATEGORIES
    public function warehouses();

    // RETURN CUSTOMERS
    public function customers();

    // RETURN CUSTOMERS BRANCHES
    public function customersBranches();

    // RETURN PRODUCT DISMISS REASONS
    public function productDismissReasons();

    // RETURN PRODUCT DISMISS REASONS
    public function expensesTypes();

    // RETURN INVOICE NUMBER BASED ON THE 5 TYPES OF INVOICES
    public function invoiceNumberGenerator($invoice_type);
}
