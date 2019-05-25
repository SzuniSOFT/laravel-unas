<?php


namespace SzuniSoft\Unas\Laravel\Events;


use Illuminate\Contracts\Validation\Validator;
use SzuniSoft\Unas\Internal\Client;

/**
 * Class InvalidConfiguration
 * @package SzuniSoft\Unas\Laravel\Events
 * @codeCoverageIgnore
 */
class InvalidConfiguration
{

    /**
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * InvalidConfiguration constructor.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @param \SzuniSoft\Unas\Internal\Client            $client
     */
    public function __construct(Validator $validator, Client $client)
    {
        $this->validator = $validator;
        $this->client = $client;
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

}
