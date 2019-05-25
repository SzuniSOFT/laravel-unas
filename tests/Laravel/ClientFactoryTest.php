<?php


namespace SzuniSoft\Unas\Tests\Laravel;


use Mockery\MockInterface;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Support\ClientFactory;
use SzuniSoft\Unas\Laravel\Support\ClientManager;
use SzuniSoft\Unas\Laravel\Support\UnasServiceProvider;
use SzuniSoft\Unas\Tests\BaseTestCase;

class ClientFactoryTest extends BaseTestCase
{


    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [UnasServiceProvider::class];
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
    public function does_not_add_client_to_manager_when_not_allowed()
    {
        /** @var \Mockery\MockInterface $manager */
        $manager = $this->mock(ClientManager::class);

        /** @var \Mockery\MockInterface $factory */
        $factory = $this->mock(ClientFactory::class);
        $factory->makePartial();
        $this->mockProperty($factory, 'rememberAllowed', false);

        $factory->create($this->getConfig());

        $manager->shouldNotHaveReceived('hasClient');
        $manager->shouldNotHaveReceived('getClient');
        $manager->shouldNotHaveReceived('addClient');
    }

    /** @test */
    public function adds_client_to_manager_when_allowed()
    {
        $config = $this->getConfig();

        $manager = $this->mock(ClientManager::class, function (MockInterface $mock) use (&$config) {

            $mock
                ->shouldReceive('hasClient')
                ->once()
                ->andReturn(false);

            $mock
                ->shouldReceive('addClient')
                ->once()
                ->withArgs(function ($receivedConfig, $client) use ($config) {
                    return (
                        $receivedConfig === $config &&
                        $client instanceof Client
                    );
                });

            $mock
                ->shouldNotReceive('getClient');

        });

        /** @var \Mockery\MockInterface $factory */
        $factory = $this->mock(ClientFactory::class);
        $this->mockProperty($factory, 'manager', $manager);
        $factory->makePartial();
        $this->mockProperty($factory, 'rememberAllowed', true);

        $factory->create($config);
    }

    /** @test */
    public function does_not_add_client_multiple_times()
    {
        $config = $this->getConfig();

        $manager = $this->mock(ClientManager::class, function (MockInterface $mock) use (&$config) {

            $mock
                ->shouldReceive('hasClient')
                ->once()
                ->andReturn(true);

            $mock
                ->shouldReceive('getClient')
                ->once()
                ->withArgs(function ($receivedConfig) use ($config) {
                    return $receivedConfig === $config;
                });

            $mock
                ->shouldNotReceive('addClient');

        });

        /** @var \Mockery\MockInterface $factory */
        $factory = $this->mock(ClientFactory::class);
        $this->mockProperty($factory, 'manager', $manager);
        $factory->makePartial();
        $this->mockProperty($factory, 'rememberAllowed', true);

        $factory->create($config);
    }

}
