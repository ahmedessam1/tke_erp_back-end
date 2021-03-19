<?php

namespace App\Repositories;

use App\Models\Expenses\ExpensesTypes;
use App\Models\Invoices\ExportInvoice;
use App\Models\Invoices\ImportInvoice;
use App\Models\Refund\Refund;
use App\Repositories\Contracts\RequirementRepository;
use App\Traits\Data\GetProductDismissReasonsList;
use App\Traits\Data\GetPaymentTypesList;
use App\Traits\Data\GetCategoriesList;
use App\Traits\Data\GetCustomersList;
use App\Traits\Data\GetPositionsList;
use App\Traits\Data\GetSuppliersList;
use App\Traits\Data\GetUsersList;
use App\Traits\Data\GetWarehousesList;
use Auth;

class EloquentRequirementRepository implements RequirementRepository
{
    use GetPaymentTypesList,
        GetSuppliersList,
        GetPositionsList,
        GetCategoriesList,
        GetCustomersList,
        GetUsersList,
        GetProductDismissReasonsList,
        GetWarehousesList;

    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function users()
    {
        if (Auth::user()->hasRole(['super_admin', 'accountant']))
            return $this->getUsersListOrderedByName();
        else
            return $this->getLoggedUserName();
    }

    public function paymentTypes()
    {
        return $this->getPaymentTypesListOrderedByName();
    }

    public function suppliers()
    {
        return $this->getSuppliersListOrderedByName();
    }

    public function positions()
    {
        return $this->getPositionsListOrderedByName();
    }

    public function categories()
    {
        return $this->getCategoriesListOrderedByName();
    }

    public function warehouses()
    {
        return $this->getWarehousesListOrderedByName();
    }

    public function customers()
    {
        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'accountant']))
            return $this->getCustomersListOrderedByName();
        else if ($user->hasRole(['super_admin', 'sales']))
            return $this->getCustomersListPerUserOrderedByName();
        else
            return 403;
    }

    public function customersBranches()
    {
        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'accountant']))
            return $this->getCustomersBranchesListOrderedByName();
        else if ($user->hasRole(['super_admin', 'sales', 'digital_marketing']))
            return $this->getCustomersBranchesListPerUserOrderedByName();
        else
            return 403;
    }

    public function productDismissReasons()
    {
        return $this->getProductDismissReasonsListOrderedByReason();
    }

    public function expensesTypes()
    {
        return ExpensesTypes::orderBy('type', 'ASC') -> pluck('type', 'id');
    }

    public function invoiceNumberGenerator($invoice_type)
    {
        // 5 TYPES OF INVOICES ['import_invoice', 'export_invoice_with_tax', 'export_invoice_without_tax', 'customer_refund, 'supplier_refund']
        $generated_invoice_number = null;
        if($invoice_type === 'import_invoice') {
            $generated_invoice_number = ImportInvoice::orderBy('number', 'DESC')->first()->number;
        } else if($invoice_type === 'export_invoice_with_tax') {
            $generated_invoice_number = ExportInvoice::where('tax', 1)->orderBy('number', 'DESC')->first()->number;
        } else if($invoice_type === 'export_invoice_without_tax') {
            $generated_invoice_number = ExportInvoice::where('tax', 0)->orderBy('number', 'DESC')->first()->number;
        } else if($invoice_type === 'customer_refund') {
            $generated_invoice_number = Refund::where('type', 'in')->orderBy('number', 'DESC')->first()->number;
        } else if($invoice_type === 'supplier_refund') {
            $generated_invoice_number = Refund::where('type', 'out')->orderBy('number', 'DESC')->first()->number;
        }

        if($generated_invoice_number)
            $generated_invoice_number = (int)$generated_invoice_number + 1;

        return $generated_invoice_number;
    }
}
