<?php

namespace App\Repositories\Contracts;

interface GeneralRepository
{
    public function attachFileToModel($request);
    public function deleteFileFromModel($id);
}
