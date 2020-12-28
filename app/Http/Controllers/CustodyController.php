<?php

namespace App\Http\Controllers;

use App\Http\Requests\Custodies\MoneyStoreRequest;
use App\Http\Requests\TableSearchRequest;
use App\Models\Custody\Custody;
use App\Repositories\Contracts\CustodyRepository;
use Illuminate\Http\Request;
use Response;

class CustodyController extends Controller
{
    protected $model;

    public function __construct(CustodyRepository $model)
    {
        $this->model = $model;
    }

    public function moneyCustodyIndex(Request $request)
    {
        return Response::json($this->model->moneyCustodyIndex($request->all()));
    }

    public function moneyCustodySearch(TableSearchRequest $request)
    {
        return Response::json($this->model->moneyCustodySearch($request->all()));
    }

    public function moneyCustodyShow($item_id)
    {
        return Response::json($this->model->moneyCustodyShow($item_id));
    }

    public function moneyCustodyStore(MoneyStoreRequest $request)
    {
        return Response::json($this->model->moneyCustodyStore($request->all()));
    }

    public function moneyCustodyUpdate($id, MoneyStoreRequest $request)
    {
        return Response::json($this->model->moneyCustodyUpdate($id, $request->all()));
    }

    public function moneyCustodyApprove(Request $request, $item_id)
    {
        $item = Custody::notApproved()->find($item_id);
        if ($item) {
            $this->validate($request, [
                'amount' => 'required|numeric|max:'.$item->amount,
            ]);
            return Response::json($this->model->moneyCustodyApprove($item_id, $request->amount));
        }
        return Response::json(['error' => 'Item not found..'], 500);
    }

    public function moneyCustodyDelete($item_id)
    {
        return Response::json($this->model->moneyCustodyDelete($item_id));
    }
}
