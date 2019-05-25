<?php


namespace SzuniSoft\Unas\Exceptions;


use SzuniSoft\Unas\Internal\Client;

/**
 * Interface EventException
 * @package SzuniSoft\Unas\Exceptions
 * @codeCoverageIgnore
 */
interface EventException
{

    /**
     * @return string
     */
    public function makeEvent();

    /**
     * @return string
     */
    public function eventClass();

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client;

}
