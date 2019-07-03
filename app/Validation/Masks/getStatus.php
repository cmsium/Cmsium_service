<?php
namespace App\Validation\Masks;
use validation\masks\Mask;

class getStatus implements Mask {

    public function mask($validator) {
        $validator->status->boolean()->required();
        $validator->space->integer()->required();
        $validator->workload->integer(11)->required();
    }
}