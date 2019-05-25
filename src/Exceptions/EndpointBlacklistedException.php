<?php


namespace SzuniSoft\Unas\Exceptions;


use Carbon\Carbon;
use RuntimeException;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Events\EndpointBlacklisted;

class EndpointBlacklistedException extends RuntimeException implements EventException
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
     * BlacklistedException constructor.
     *
     * @param string                          $endpoint
     * @param \Carbon\Carbon                  $until
     * @param \SzuniSoft\Unas\Internal\Client $client
     */
    public function __construct(string $endpoint, Carbon $until, Client $client)
    {
        $untilString = $until->format('Y-m-d H:i:s');
        parent::__construct("Endpoint [$endpoint] being blacklisted [$untilString]");
        $this->endpoint = $endpoint;
        $this->until = $until;
        $this->client = $client;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
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
     * @return mixed
     */
    public function makeEvent()
    {
        return new EndpointBlacklisted($this->endpoint, $this->until, $this->client);
    }

    /**
     * @return string
     */
    public function eventClass()
    {
        return EndpointBlacklisted::class;
    }
}
