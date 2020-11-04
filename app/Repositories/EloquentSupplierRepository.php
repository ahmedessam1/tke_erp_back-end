<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Events\TransactionHappened;
use App\Models\Invoices\ImportInvoice;
use App\Repositories\Contracts\SupplierRepository;
use App\Events\ActionHappened;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierPayment;
use App\Traits\Logic\InvoiceCalculations;
use App\Traits\Logic\SupplierCreditCalculations;
use Auth;
use DB;

class EloquentSupplierRepository implements SupplierRepository {
    use InvoiceCalculations, SupplierCreditCalculations;
    
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }
    
    private function getAuthUserId() {
        return Auth::user()->id;
    }

    public function getAllActiveSuppliers ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        $redis_key = 'suppliers:'.$request['page'].':sort_by:'.$request['sort_by'].':sort_type:'.$request['sort_type'];

        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $suppliers = $this->cache->remember($redis_key, function () use ($sorting) {
            return json_encode(Supplier::withCreatedByAndUpdatedBy()
                ->getAddresses()
                ->getAddressesContacts()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30));
        }, config('constants.cache_expiry_minutes_small'));
        return json_decode($suppliers);
    }

    public function getSuppliersSearchResult ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];
        
        $suppliers = Supplier::withCreatedByAndUpdatedBy()->orderedName()->getAddresses()->getAddressesContacts()
           ->where('name', 'LIKE', '%'.$q.'%')
           ->orWhereDate('created_at', 'LIKE', '%'.$q.'%')
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
           ->paginate(30);
        return $suppliers;
    }

    public function showSupplier ($supplier_id) {
        $supplier = Supplier::withCreatedByAndUpdatedBy()->getAddresses()->getAddressesContacts()->find($supplier_id);
        return $supplier;
    }

    public function addSupplier ($request) {
        // DELETE CACHED SUPPLIERS
        $this->cache->forgetByPattern('suppliers:*');
        return DB::transaction(function () use ($request) {
            // ADDING THE SUPPLIER BASIC INFO
            $supplier_fillable_values = array_merge(
                $request->except('address_contact_inputs'),
                ['created_by' => $this->getAuthUserId()]
            );
            $new_supplier = Supplier::create($supplier_fillable_values);

            $this->insertingSupplier($request, $new_supplier);

            // STORE ACTION
            event(new ActionHappened('supplier added', $request, $this->getAuthUserId()));
            return $new_supplier;
        });
    }

    public function editSupplier ($supplier_id) {
        $edited_season = Supplier::withCreatedByAndUpdatedBy()->getAddressesAndContacts()->find($supplier_id);
        return $edited_season;
    }

    public function updateSupplier ($request, $supplier_id)
    {
        // DELETE CACHED SUPPLIERS
        $this->cache->forgetByPattern('suppliers:*');
        // GETTING THE SUPPLIER
        $updated_supplier = Supplier::withCreatedByAndUpdatedBy()->find($supplier_id);

        return DB::transaction(function () use ($updated_supplier, $request) {
            // SUPPlIER BASIC INFO UPDATE
            $supplier_fillable_values = array_merge(
                $request->except('address_contact_inputs'),
                ['updated_by' => $this->getAuthUserId()]
            );
            $updated_supplier->update($supplier_fillable_values);

            // DELETING THE ADDRESSES
            foreach($updated_supplier->addresses as $address)
                $address->contacts()->delete();
            $updated_supplier->addresses()->delete();

            // ADDING THE SUPPLIER ADDRESSES AND THE ADDRESS CONTACTS
            $this->insertingSupplier($request, $updated_supplier);


            event(new ActionHappened('supplier updated', $updated_supplier->id, $this->getAuthUserId()));
            return $updated_supplier;
        });
    }

    public function deleteSupplier ($supplier) {
        // DELETE CACHED SUPPLIERS
        $this->cache->forgetByPattern('suppliers:*');
        // DELETING THE SUPPLIER
        $supplier->delete();
        // STORE ACTION
        event(new ActionHappened('supplier deleted', $supplier->id, $this->getAuthUserId()));
        return $supplier;
    }

    public function restoreSupplier ($supplier_id) {
        // DELETE CACHED SUPPLIERS
        $this->cache->forgetByPattern('suppliers:*');
        // RESTORING THE SUPPLIER
        $supplier = Supplier::withTrashed()->find($supplier_id);
        $supplier->restore();
        // STORE ACTION
        event(new ActionHappened('supplier restored', $supplier, $this->getAuthUserId()));
        return $supplier;
    }

    public function addresses($supplier_id) {
        return Supplier::getAddresses()->getAddressesAndContacts()->find($supplier_id);
    }


    /* *********************************************
     * ************* SUPPLIER INVOICES *************
     * *********************************************/
    // INVOICES
    public function invoices ($supplier_id) {
        $supplier_invoices = ImportInvoice::approved()->where('supplier_id', $supplier_id)->paginate(30);
        return $supplier_invoices;
    }

    // INVOICES SEARCH
    public function invoicesSearch ($q, $supplier_id) {
        $invoices = ImportInvoice::approved()->withSupplier()->withCreatedByAndUpdatedBy()->orderedName()
           ->where('supplier_id', $supplier_id)
           ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
                $query->orWhere('number', 'LIKE', '%'.$q.'%');
                $query->orWhere('date', 'LIKE', '%'.$q.'%');
                    // SEARCH FOR SUPPLIER NAME
                $query->orWhereHas('supplier', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%'.$q.'%');
                });
            })
           ->paginate(30);
        return $invoices;
    }

    // CREDIT
    public function credit ($supplier_id) {
        // CALCULATE THE INVOICES TOTAL
        $invoices_total = $this->supplierInvoicesTotal($supplier_id);

        // CALCULATE THE SUPPLIER PAYMENTS TOTAL
        $payments_total = $this->supplierPaymentsTotal($supplier_id);

        // CALCULATE THE SUPPLIER INITIATORY CREDIT
        $initiatory_credit_total = $this->supplierInitiatoryCreditTotal($supplier_id);

        // CALCULATE REFUNDS
        $refunds_total = $this->supplierRefundsTotal($supplier_id);

        // CALCULATE NET CREDIT
        $net_credit = $this->netCredit();

        return [
            'invoices_total'    => $invoices_total,
            'payments_total'    => $payments_total,
            'initiatory_credit_total' => $initiatory_credit_total,
            'refunds_total' => $refunds_total,
            'net_credit'        => $net_credit,
        ];
    }


    /* *********************************************
     * ************* SUPPLIER PAYMENTS *************
     * *********************************************/
    public function payments () {
        return SupplierPayment::with('paymentType')
           ->with('supplier')
           ->orderBy('approve', 'ASC')
           ->paginate(30);
    }

    public function paymentsSearch ($q) {
        $suppliers_payments = SupplierPayment::with('paymentType')
           ->with('supplier')
           ->where('amount', 'LIKE', '%'.$q.'%')
           ->orWhereHas('supplier', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
           ->orWhereHas('paymentType', function ($query) use ($q) {
                $query->where('type', 'LIKE', '%'.$q.'%');
            })
           ->orWhere('notes', 'LIKE', '%'.$q.'%')
           ->orWhereDate('created_at', 'LIKE', '%'.$q.'%')
           ->paginate(30);
        return $suppliers_payments;
    }

    public function paymentsAdd ($request) {
        return DB::transaction(function () use ($request) {
            // ADDING THE SUPPLIER PAYMENT
            $payment_fillable_values = array_merge(
                $request->all(),
                ['created_by' => $this->getAuthUserId()]
            );

            $new_supplier_payment = SupplierPayment::create($payment_fillable_values);

            // STORE ACTION
            event(new ActionHappened('supplier payment add: '.$request->amount, $request, $this->getAuthUserId()));
            return $new_supplier_payment;
        });
    }

    public function paymentsApprove ($payment_id) {
        return DB::transaction(function () use ($payment_id) {
            $payment = SupplierPayment::find($payment_id);
            $payment->approve = 1;
            $payment->save();
            // TRANSACTION EVENT STORE
            event(new TransactionHappened([
                'model_type'        => 'App\Models\Supplier\SupplierPayment',
                'model_id'          => $payment->id,
                'case'              => 'out',
                'payment_type_id'   => $payment->payment_type_id,
                'amount'            => $payment->amount,
                'created_by'        => $this->getAuthUserId(),
            ]));

            // STORE ACTION
            event(new ActionHappened('supplier payment approved: '.$payment_id, $payment, $this->getAuthUserId()));
            return $payment;
        });
    }

    public function paymentsDelete($payment_id) {
        $payment = SupplierPayment::notApproved()->where('id', $payment_id)->first();
        $payment->delete();
        event(new ActionHappened('supplier payment deleted: '.$payment_id, $payment, $this->getAuthUserId()));
        return $payment;
    }

    public function paymentsShow($payment_id) {
        return SupplierPayment::with('supplier')->with('supplierAddress')->with('supplierContact')->find($payment_id);
    }


    /* *********************************************
     * ************** PRIVATE HELPERS **************
     * *********************************************/
    // STORING THE SUPPLIER
    private function insertingSupplier ($request, $new_supplier) {
        // ADDING THE SUPPLIER ADDRESSES AND THE ADDRESS CONTACTS
        foreach ($request->address_contact_inputs as $address) {
            // ADD ADDRESS
            $new_supplier_address = $new_supplier->addresses()->create([
                'address'       => $address['address'],
                'created_by'    => $this->getAuthUserId()
            ]);

            // ADD ADDRESS CONTACTS
            foreach ($address['contacts'] as $contact)
                $new_supplier_address->contacts()->create([
                    'name'          => $contact['name'],
                    'phone_number'  => $contact['phone_number'],
                    'position_id'   => $contact['position_id'],
                    'created_by'    => $this->getAuthUserId()
                ]);
        }
    }

    /**
     * @param $sort_by
     * @param $sort_type
     * @return array
     */
    private function setSorting ($sort_by, $sort_type) {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}