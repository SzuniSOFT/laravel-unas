<?php


namespace SzuniSoft\Unas\Tests\Client\__stubs__;


use RuntimeException;
use SzuniSoft\Unas\Exceptions\EventException;
use SzuniSoft\Unas\Internal\Client;

class EventExceptionStub extends RuntimeException implements EventException
{

    /**
     * @var \SzuniSoft\Unas\Internal\Client
     */
    private $client;


    /**
     * @param \SzuniSoft\Unas\Internal\Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function makeEvent()
    {
        return new EventStub();
    }

    /**
     * @return string
     */
    public function eventClass()
    {
        return EventStub::class;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
