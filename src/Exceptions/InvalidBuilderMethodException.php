<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;

class InvalidBuilderMethodException extends RuntimeException
{

    public function __construct($method, $builder)
    {
        parent::__construct("Invalid method [$method] invoked of builder [$builder]", null, null);
    }
}
