<?php

namespace Tests\Unit;

use App\Traits\Logic\GenerateLocalCode;
use Tests\TestCase;

class LocalCodeTest extends TestCase
{
    use GenerateLocalCode;
    public function test_local_code_generation()
    {
        // GENERATING THE NEW LOCAL_CODE
        $local_code = $this->getLocalCode();
        $local_code_length = strlen($local_code);

        $this -> assertEquals($local_code_length, 13);
    }
}
