<?php


namespace SzuniSoft\Unas\Laravel\Support;


use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use SzuniSoft\Unas\Internal\Client;
use function config_path;

class UnasServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Arr::macro('wrapNumeric', function ($arr) {

            $arr = Arr::wrap($arr);

            if (Arr::isAssoc($arr)) {
                return [$arr];
            }

            return $arr;
        });
    }

    public function register()
    {
        $this->registerConfigs();
        $this->registerClient();
        $this->registerClientFactory();
        $this->registerClientBuilder();
        $this->registerClientManager();
    }

    protected function registerClient()
    {
        $this->app->bind('unas.client', function (Container $app) {
            return $app->get('unas.client.factory')->create(
                $app->get('config')['unas']
            );
        });
        $this->app->alias('unas.client', Client::class);
    }

    protected function registerClientFactory()
    {
        $this->app->singleton('unas.client.factory', function (Container $app) {
            return new ClientFactory(
                $app->get('config')['unas']['global'],
                $app->get('unas.client.manager')
            );
        });
        $this->app->alias('unas.client.factory', ClientFactory::class);
    }

    protected function registerClientBuilder()
    {
        $this->app->singleton('unas.client.builder', ClientBuilder::class);
    }

    protected function registerClientManager()
    {
        $this->app->singleton('unas.client.manager', ClientManager::class);
    }

    /**
     * Register and offer config.
     */
    protected function registerConfigs()
    {
        $configPath = __DIR__ . '/../../../config/config.php';
        $this->mergeConfigFrom($configPath, 'unas');
        $this->publishes([
            $configPath => config_path('unas.php'),
        ], 'config');
    }
}
