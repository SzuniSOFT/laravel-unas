<?php


namespace SzuniSoft\Unas\Exceptions;


use Illuminate\Contracts\Validation\Validator;
use RuntimeException;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Events\InvalidConfiguration;

/**
 * Class InvalidConfigurationException
 * @package SzuniSoft\Unas\Exceptions
 * @codeCoverageIgnore
 */
class InvalidConfigurationException extends RuntimeException implements EventException
{

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * InvalidConfigurationException constructor.
     *
     * @param Validator                       $validator
     * @param \SzuniSoft\Unas\Internal\Client $client
     */
    public function __construct(Validator $validator, Client $client)
    {
        parent::__construct($validator->errors()->toJson());
        $this->validator = $validator;
        $this->client = $client;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return string
     */
    public function makeEvent()
    {
        return new InvalidConfiguration($this->validator, $this->client);
    }

    /**
     * @return string
     */
    public function eventClass()
    {
        return InvalidConfiguration::class;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
