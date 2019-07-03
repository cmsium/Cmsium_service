<?php


namespace App\Exceptions;


class ValidationException extends \Exception {
    protected $message = "Validation error";
    protected $code = 422;
}