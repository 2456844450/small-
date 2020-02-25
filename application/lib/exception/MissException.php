<?php


namespace app\lib\exception;


use think\Exception;

class MissException extends Exception
{
    public $code = 404;
    public $msg = 'global:your required resource are not found';
    public $errorCode = 10001;
}