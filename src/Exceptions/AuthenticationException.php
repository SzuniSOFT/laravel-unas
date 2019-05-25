<?php


namespace SzuniSoft\Unas\Exceptions;


use RuntimeException;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Events\Unauthenticated;

class AuthenticationException extends RuntimeException implements EventException
{

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * @var null
     */
    protected $field;

    /**
     * AuthenticationException constructor.
     *
     * @param \SzuniSoft\Unas\Internal\Client $client
     * @param null                            $field
     */
    public function __construct(Client $client, $field = null)
    {
        parent::__construct("Authentication failed.");
        $this->client = $client;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function makeEvent()
    {
        return new Unauthenticated($this->client, $this);
    }

    /**
     * @return string
     */
    public function eventClass()
    {
        return Unauthenticated::class;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return null
     */
    public function getField()
    {
        return $this->field;
    }
}
