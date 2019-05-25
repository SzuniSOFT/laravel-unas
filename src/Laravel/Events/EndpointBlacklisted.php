<?php


namespace SzuniSoft\Unas\Laravel\Events;


use Carbon\Carbon;
use SzuniSoft\Unas\Internal\Client;

class EndpointBlacklisted
{

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var \Carbon\Carbon
     */
    protected $until;

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    protected $client;

    /**
     * EndpointBlacklisted constructor.
     *
     * @param string                          $endpoint
     * @param \Carbon\Carbon                  $until
     * @param \SzuniSoft\Unas\Internal\Client $client
     */
    public function __construct(string $endpoint, Carbon $until, Client $client)
    {
        $this->endpoint = $endpoint;
        $this->until = $until;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getUntil(): Carbon
    {
        return $this->until;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

}
