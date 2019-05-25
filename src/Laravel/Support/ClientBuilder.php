<?php


namespace SzuniSoft\Unas\Laravel\Support;

use function array_search;
use function in_array;

/**
 * Class ClientBuilder
 * @package SzuniSoft\Unas\Laravel\Support
 */
class ClientBuilder
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \SzuniSoft\Unas\Laravel\Support\ClientFactory
     */
    protected $clientFactory;

    /**
     * ClientBuilder constructor.
     *
     * @param \SzuniSoft\Unas\Laravel\Support\ClientFactory $clientFactory
     * @param array                                         $defaultConfig
     */
    public function __construct(ClientFactory $clientFactory, array $defaultConfig = [])
    {
        $this->clientFactory = $clientFactory;
        $this->config = $defaultConfig;
        $this->config['events'] = $this->config['events'] ?? [];
    }

    /**
     * @param $apiKey
     *
     * @return \SzuniSoft\Unas\Laravel\Support\ClientBuilder
     */
    public function withPremium($apiKey)
    {
        unset($this->config['username']);
        unset($this->config['password']);
        unset($this->config['shop_id']);
        unset($this->config['auth_code']);
        $this->config['key'] = $apiKey;

        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @param $shopId
     * @param $authCode
     *
     * @return \SzuniSoft\Unas\Laravel\Support\ClientBuilder
     */
    public function withLegacy($username, $password, $shopId, $authCode)
    {
        unset($this->config['key']);
        $this->config['username'] = $username;
        $this->config['password'] = $password;
        $this->config['shop_id'] = $shopId;
        $this->config['auth_code'] = $authCode;

        return $this;
    }

    /**
     * @param $path
     *
     * @return \SzuniSoft\Unas\Laravel\Support\ClientBuilder
     */
    public function basePath($path)
    {
        $this->config['base_path'] = $path;

        return $this;
    }

    /**
     * @param mixed ...$eventClasses
     *
     * @return $this
     */
    public function allowedEvents(...$eventClasses)
    {
        foreach ($eventClasses as $eventClass) {
            if (!in_array($eventClass, $this->config['events'])) {
                $this->config['events'][] = $eventClass;
            }
        }

        return $this;
    }

    /**
     * @param mixed ...$eventClasses
     *
     * @return $this
     */
    public function disallowedEvents(...$eventClasses)
    {
        foreach ($eventClasses as $eventClass) {
            $index = array_search($eventClass, $this->config['events']);
            if ($index !== false) {
                unset($this->config['events'][$index]);
            }
        }
        return $this;
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Client
     */
    public function build()
    {
        return $this->clientFactory->create($this->config);
    }

    /**
     * @return array
     */
    public function config()
    {
        return $this->config;
    }

}
