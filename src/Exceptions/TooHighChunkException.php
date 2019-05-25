<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;

class TooHighChunkException extends RuntimeException
{

    protected $maxAllowed;

    protected $given;

    /**
     * TooHighChunkException constructor.
     *
     * @param $maxAllowed
     * @param $given
     */
    public function __construct($maxAllowed, $given)
    {
        parent::__construct("Tried to retrieve [$given] amount of data however max available paging chunk size is restricted to [$maxAllowed]!");
        $this->maxAllowed = $maxAllowed;
        $this->given = $given;
    }

    public function getMaxAllowed()
    {
        return $this->maxAllowed;
    }

    public function getGiven()
    {
        return $this->given;
    }

}
