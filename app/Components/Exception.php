<?php

namespace App\Components;


class Exception extends \Exception implements \Throwable
{

    /**
     * 通过数组构建
     * @param array $error
     * @param Exception $previous
     * @return Exception
     */
    public static function buildFromArray(array $error, Exception $previous = null)
    {
        list($code, $msg) = $error;

        return new self($msg, $code, $previous);
    }


}