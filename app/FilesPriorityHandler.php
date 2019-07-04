<?php
namespace App;

class FilesPriorityHandler extends PriorityHandler {

    public $priorities = [
        'status' => ['weight' => 0.7, 'min' => 0, 'max' => 1],
        'space' => ['weight' => 0.1, 'min' => 0, 'max' => 2000000000000],
        'workload' => ['weight' => 0.2, 'min' => 0, 'max' => 3000],
    ];

    public function getValue($name, $value) {
        switch ($name){
            case 'workload': return $this->getMax($name) - $value; break;
            default: return $value;
        }
    }

}