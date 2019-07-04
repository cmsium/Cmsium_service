<?php
namespace App\Exceptions;

class PriorityWeightsException extends \Exception {
    protected $message = "Summ of priority weights must be equal to 1";
    protected $code = 500;
}