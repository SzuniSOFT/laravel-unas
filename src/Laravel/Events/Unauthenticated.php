<?php


namespace SzuniSoft\Unas\Laravel\Events;


use SzuniSoft\Unas\Exceptions\AuthenticationException;
use SzuniSoft\Unas\Internal\Client;

/**
 * Class Unauthenticated
 * @package SzuniSoft\Unas\Laravel\Events
 * @codeCoverageIgnore
 */
class Unauthenticated
{

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * @var \SzuniSoft\Unas\Exceptions\AuthenticationException
     */
    protected $exception;

    /**
     * Unauthenticated constructor.
     *
     * @param \SzuniSoft\Unas\Internal\Client                    $client
     * @param \SzuniSoft\Unas\Exceptions\AuthenticationException $exception
     */
    public function __construct(Client $client, AuthenticationException $exception)
    {
        $this->client = $client;
        $this->exception = $exception;
    }

    /**
     * @return \SzuniSoft\Unas\Exceptions\AuthenticationException
     */
    public function getException(): AuthenticationException
    {
        return $this->exception;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

}
