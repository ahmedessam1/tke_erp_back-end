<?php

namespace App\Repositories;

use App\Models\Custody\Custody;
use App\Repositories\Contracts\CustodyRepository;
use Auth;

class EloquentCustodyRepository implements CustodyRepository
{
    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function moneyCustodyIndex($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        return Custody::withUser()->withPaymentType()->orderBy($sorting['sort_by'], $sorting['sort_type'])->paginate(30);
    }

    public function moneyCustodySearch($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return Custody::withUser()->withPaymentType()->where('title', 'LIKE', '%' . $q . '%')
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function moneyCustodyShow($item_id)
    {
        return Custody::withUser()->withPaymentType()->find($item_id);
    }

    public function moneyCustodyStore($request)
    {
        $fillable_values = array_merge(
            $request,
            ['created_by' => $this->getAuthUserId()]
        );
        return Custody::create($fillable_values);
    }

    public function moneyCustodyUpdate($id, $request)
    {
        $update_data = array_merge(
            $request,
            ['updated_by' => $this->getAuthUserId()]
        );
        $data = Custody::where('id', $id)->where('approve', 0)->first();
        $data->update($update_data);

        return $data;
    }

    public function moneyCustodyApprove($item_id, $amount)
    {
        $approved = Custody::withUser()->withPaymentType()->notApproved()->find($item_id);
        if ($approved) {
            $approved->approve = 1;
            $approved->spent_amount = $amount;
            $approved->save();
        }
        return $approved;
    }

    public function moneyCustodyDelete($item_id)
    {
        $deleted = Custody::notApproved()->find($item_id);
        if ($deleted)
            $deleted->delete();
        return $deleted;
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */
    private function setSorting($sort_by, $sort_type)
    {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}
