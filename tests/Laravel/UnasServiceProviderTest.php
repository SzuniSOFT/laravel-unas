<?php


namespace SzuniSoft\Unas\Tests\Laravel;


use function dd;
use Mockery\MockInterface;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Laravel\Support\ClientBuilder;
use SzuniSoft\Unas\Laravel\Support\ClientFactory;
use SzuniSoft\Unas\Laravel\Support\ClientManager;
use SzuniSoft\Unas\Laravel\Support\UnasServiceProvider;
use SzuniSoft\Unas\Tests\BaseTestCase;
use function is_string;

class UnasServiceProviderTest extends BaseTestCase
{

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('unas', [
            'driver' => 'key',
            'key' => 'key',
            'global' => [],
            'base_path' => 'http://example.com/',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [UnasServiceProvider::class];
    }

    /** @test */
    public function registers_necessary_services()
    {
        $this->assertInstanceOf(Client::class, $this->app->get('unas.client'));
        $this->assertInstanceOf(Client::class, $this->app->get(Client::class));

        $this->assertInstanceOf(ClientFactory::class, $this->app->get('unas.client.factory'));
        $this->assertInstanceOf(ClientFactory::class, $this->app->get(ClientFactory::class));

        $this->assertInstanceOf(ClientBuilder::class, $this->app->get('unas.client.builder'));
        $this->assertInstanceOf(ClientBuilder::class, $this->app->get(ClientBuilder::class));

        $this->assertInstanceOf(ClientManager::class, $this->app->get('unas.client.manager'));
        $this->assertInstanceOf(ClientManager::class, $this->app->get(ClientManager::class));
    }

    /** @test */
    public function publishes_config()
    {
        $service = $this->mock(UnasServiceProvider::class, function (MockInterface $mock) {

            $mock->shouldAllowMockingProtectedMethods();
            $mock->makePartial();

            $mock->shouldReceive('mergeConfigFrom')
                ->withArgs(function ($path, $key) {
                    return is_string($path) && $key === 'unas';
                })
                ->once();

            $mock->shouldReceive('publishes')
                ->withArgs(function ($paths, $groups) {
                    return $groups === 'config';
                })
                ->once();
        });

        $service->registerConfigs();
    }

}
