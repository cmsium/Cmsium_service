<?php
namespace App\Exceptions;

class FileServerConnectError extends \Exception {
    protected $message = "Can not connect to file server";
    protected $code = 500;
}