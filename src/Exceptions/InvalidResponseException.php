<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;
use Throwable;

class InvalidResponseException extends RuntimeException
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Could not process the response coming from UNAS.', $previous->getCode(), $previous);
    }

}
