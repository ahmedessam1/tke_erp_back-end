<?php

namespace App\Repositories\Contracts;

interface CustodyRepository
{
    public function moneyCustodyIndex($request);

    public function moneyCustodySearch($request);

    public function moneyCustodyShow($item_id);

    public function moneyCustodyStore($request);

    public function moneyCustodyUpdate($id, $request);

    public function moneyCustodyApprove($item_id, $amount);

    public function moneyCustodyDelete($item_id);
}
