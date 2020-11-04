<?php

namespace App\Traits\Data;

use App\Models\Requirements\EloquentRequirementRepository;
use App\Models\Requirements\PaymentType;

trait GetPaymentTypesList {
    public function getPaymentTypesListOrderedByName () {
        return PaymentType::pluck('type', 'id');
    }

    public function getPaymentTypesListOrderedByID () {
        return PaymentType::pluck('type', 'id');
    }
}