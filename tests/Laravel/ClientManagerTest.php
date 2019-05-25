<?php


namespace SzuniSoft\Unas\Tests\Laravel;


use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Support\ClientManager;
use SzuniSoft\Unas\Laravel\Support\UnasServiceProvider;
use SzuniSoft\Unas\Tests\BaseTestCase;

class ClientManagerTest extends BaseTestCase
{

    /**
     * @var ClientManager
     */
    private $manager;

    protected function getPackageProviders($app)
    {
        return [UnasServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->app->get(ClientManager::class);
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            'driver' => 'key',
            'key' => 'key',
            'base_path' => 'http://example.com/',
        ];
    }

    /** @test */
    public function can_add_client_to_pool()
    {
        $config = $this->getConfig();
        $this->manager->addClient($config, new Client($config));
        $this->assertTrue($this->manager->hasClient($config));
    }

    /** @test */
    public function can_get_client()
    {
        $config = $this->getConfig();
        $this->manager->addClient($config, $client = new Client($config));
        $this->assertSame($client, $this->manager->getClient($config));
    }

    /** @test */
    public function can_remove_client()
    {
        $config = $this->getConfig();
        $this->manager->addClient($config, $client = new Client($config));
        $this->manager->removeClient($config);
        $this->assertTrue($this->manager->getClients()->isEmpty());
    }
}
