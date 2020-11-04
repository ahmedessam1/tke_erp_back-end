<?php

namespace App\Repositories\Contracts;

interface InitiatoryRepository {
    // PRODUCTS
    // PRODUCT CREDITS INDEX
    public function productCreditsIndex();
    // PRODUCT CREDITS SEARCH
    public function productCreditsSearch($query);
    // PRODUCT CREDITS STORE
    public function productCreditsStore($request);
    // PRODUCT CREDITS DELETE
    public function productCreditsDelete($product_credit_id);


    // SUPPLIERS
    // SUPPLIER CREDITS INDEX
    public function supplierCreditsIndex();
    // SUPPLIER CREDITS SEARCH
    public function supplierCreditsSearch($query);
    // SUPPLIER CREDITS STORE
    public function supplierCreditsStore($request);
    // SUPPLIER CREDITS DELETE
    public function supplierCreditsDelete($supplier_credit_id);


    // CUSTOMERS BRANCHES
    // CUSTOMER BRANCH CREDITS INDEX
    public function customerCreditsIndex();
    // CUSTOMER BRANCH CREDITS SEARCH
    public function customerCreditsSearch($query);
    // CUSTOMER BRANCH CREDITS STORE
    public function customerCreditsStore($request);
    // CUSTOMER BRANCH CREDITS DELETE
    public function customerCreditsDelete($customer_credit_id);
}
