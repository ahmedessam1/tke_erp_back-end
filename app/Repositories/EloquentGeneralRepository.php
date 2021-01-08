<?php

namespace App\Repositories;

use App\Models\General\AttachedFiles;
use App\Repositories\Contracts\GeneralRepository;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EloquentGeneralRepository implements GeneralRepository
{
    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function attachFileToModel($request)
    {
        return DB::transaction(function() use ($request) {
            $file_name = uploadFileHelper($request->file);

            return AttachedFiles::create([
                'model_type' => $request->model_type,
                'model_id' => $request->model_id,
                'file' => $file_name,
                'created_by' => $this->getAuthUserId(),
            ]);
        });
    }

    public function deleteFileFromModel($id)
    {
        return AttachedFiles::find($id)->delete();
    }
}
