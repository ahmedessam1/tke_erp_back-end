<?php

namespace App\Repositories;

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

class EloquentRequirementRepository implements RequirementRepository {
    use GetPaymentTypesList,
        GetSuppliersList,
        GetPositionsList,
        GetCategoriesList,
        GetCustomersList,
        GetUsersList,
        GetProductDismissReasonsList,
        GetWarehousesList;

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function users () {
        return $this -> getUsersListOrderedByName();
    }

    public function paymentTypes () {
        return $this -> getPaymentTypesListOrderedByName();
    }

    public function suppliers () {
        return $this -> getSuppliersListOrderedByName();
    }

    public function positions () {
        return $this -> getPositionsListOrderedByName();
    }

    public function categories () {
        return $this -> getCategoriesListOrderedByName();
    }

    public function warehouses () {
        return $this -> getWarehousesListOrderedByName();
    }

    public function customers () {
        $user = Auth::user();
        if ($user->hasRole('super_admin'))
            return $this -> getCustomersListOrderedByName();
        else if ($user->hasRole('super_admin'))
            return $this -> getCustomersListPerUserOrderedByName();
        else
            return 403;
    }

    public function customersBranches () {
        $user = Auth::user();
        if ($user->hasRole('super_admin'))
            return $this -> getCustomersBranchesListOrderedByName();
        else if ($user->hasRole('super_admin'))
            return $this -> getCustomersBranchesListPerUserOrderedByName();
        else
            return 403;
    }

    public function productDismissReasons () {
        return $this -> getProductDismissReasonsListOrderedByReason();
    }
}
