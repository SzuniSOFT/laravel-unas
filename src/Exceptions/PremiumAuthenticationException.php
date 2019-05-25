<?php


namespace SzuniSoft\Unas\Exceptions;


use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Events\Unauthenticated;

class PremiumAuthenticationException extends AuthenticationException
{


    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * AuthenticationException constructor.
     *
     * @param \SzuniSoft\Unas\Internal\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->message = "You need a premium subscription in order to authenticate with API keys! Please consider using legacy credentials to log in!";
        $this->client = $client;
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
}
