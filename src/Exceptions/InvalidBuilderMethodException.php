<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;

/**
 * Class InvalidBuilderMethodException
 * @package SzuniSoft\Unas\Exceptions
 * @codeCoverageIgnore
 */
class InvalidBuilderMethodException extends RuntimeException
{

    public function __construct($method, $builder)
    {
        parent::__construct("Invalid method [$method] invoked of builder [$builder]", null, null);
    }
}
