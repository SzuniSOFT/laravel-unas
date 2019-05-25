<?php


namespace SzuniSoft\Unas\Laravel\Support;


use SzuniSoft\Unas\Internal\Client;
use function is_array;
use function md5;

class ClientManager
{

    /**
     * @var \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection|null
     */
    protected $clients = null;


    public function __construct()
    {
        $this->clients = collect();
    }

    /**
     * @param array $config
     *
     * @return string
     */
    protected function configToHash(array $config)
    {
        if (isset($config['key'])) {
            return md5('key:' . $config['key']);
        }
        else {
            return md5(
                'username:' . $config['username']
                . 'password:' . $config['password']
                . 'shop_id:' . $config['shop_id']
                . 'auth_code:' . $config['auth_code']
            );
        }
    }

    /**
     * @param array                           $config
     * @param \SzuniSoft\Unas\Internal\Client $client
     *
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function addClient(array $config, Client $client)
    {
        $this->clients->put(
            $this->configToHash($config),
            $client
        );

        return $client;
    }

    /**
     * @param $configOrClient
     *
     * @return bool
     */
    public function hasClient($configOrClient)
    {
        return is_array($configOrClient)
            ? $this->clients->has($this->configToHash($configOrClient))
            : $this->clients->first(function (Client $client) use (&$configOrClient) {
                return $client === $configOrClient;
            }) !== null;
    }

    /**
     * @param array $config
     *
     * @param null  $default
     *
     * @return Client|null
     */
    public function getClient(array $config, $default = null)
    {
        return $this->clients->get($this->configToHash($config), $default);
    }

    /**
     * @param $configOrClient
     */
    public function removeClient($configOrClient)
    {
        if ($this->hasClient($configOrClient)) {
            $this->clients = $this->clients->forget(
                $this->configToHash($configOrClient)
            );
        }
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection|null
     */
    public function getClients()
    {
        return $this->clients;
    }
}
