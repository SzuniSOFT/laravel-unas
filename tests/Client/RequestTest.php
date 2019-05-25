<?php


namespace SzuniSoft\Unas\Tests\Client;


use Mockery\MockInterface;
use SzuniSoft\Unas\Exceptions\EndpointBlacklistedException;
use function call_user_func;

class RequestTest extends TestCase
{


    /** @test */
    public function detects_endpoint_blacklist()
    {
        $client = $this->createSnapshotClient(function (MockInterface $mock) {
            $mock->allows('authorize');
        });

        try {
            call_user_func([$client, 'request'], ['getNewsletter']);
        } catch (EndpointBlacklistedException $exception) {
            $this->assertSame('getNewsletter', $exception->getEndpoint());
            $this->assertSame('2019-05-24 18:14:01', $exception->getUntil()->format('Y-m-d H:i:s'));
        }
    }

}
