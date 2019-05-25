<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;
use Throwable;

/**
 * Class InvalidResponseException
 * @package SzuniSoft\Unas\Exceptions
 * @codeCoverageIgnore
 */
class InvalidResponseException extends RuntimeException
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Could not process the response coming from UNAS.', $previous->getCode(), $previous);
    }

}
