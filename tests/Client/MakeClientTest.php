<?php


namespace SzuniSoft\Unas\Tests\Client;


use ReflectionClass;
use SzuniSoft\Unas\Internal\Client;
use function array_merge;

class MakeClientTest extends TestCase
{

    /**
     * @var \Mockery\MockInterface|Client
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client(array_merge(
            $this->fakeLegacyCredentials(),
            [
                'base_path' => 'http://example.com',
            ]
        ));
    }

    /** @test */
    public function can_create_http_client()
    {

        $r = new ReflectionClass($this->client);
        $m = $r->getMethod('makeClient');
        $m->setAccessible(true);

        /** @var \GuzzleHttp\Client $httpClient */
        $httpClient = $m->invoke($this->client);

        $this->assertEquals('http://example.com', $httpClient->getConfig()['base_url']);
    }

}
