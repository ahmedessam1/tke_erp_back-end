<?php

namespace App\Traits\Logic;

use App\Models\Product\LocalCode;

trait GenerateLocalCode {
    public function getLocalCode()
    {
        while (true) {
            $local_code = $this -> localCodeGeneration();
            $check = LocalCode::where('local_code', $local_code) -> exists();
            if (!$check)
                return $local_code;
        }
    }

    protected function localCodeGeneration () {
        $local_code = null;

        // SET LOCAL_CODE FIRST SECTION
        $CONSTANT_DIGIT = 4;
        for ($x = 0; $x < 3; $x++)
            $local_code .= $CONSTANT_DIGIT;

        // SET LOCAL_CODE SECOND SECTION
        $local_code .= str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // SET LOCAL_CODE THIRD SECTION
        $local_code .= $this -> generateIncrementalNumber();

        return $local_code;
    }

    protected  function generateIncrementalNumber () {
        $latest_local_code = LocalCode::orderBy('id', 'DESC') -> first();
        if ($latest_local_code != null) {
            $digits = intval(substr($latest_local_code, -5));
            $digits++;
            return str_pad($digits, 5, '0', STR_PAD_LEFT);
        } else // if first local_code inserted
            return "00001";

    }
}