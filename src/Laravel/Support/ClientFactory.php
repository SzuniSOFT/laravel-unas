<?php


namespace SzuniSoft\Unas\Laravel\Support;


use SzuniSoft\Unas\Internal\Client;
use function array_merge;

/**
 * Class ClientFactory
 * @package SzuniSoft\Unas\Laravel\Support
 */
class ClientFactory
{

    /**
     * @var bool
     */
    protected $rememberAllowed = true;

    /**
     * @var \SzuniSoft\Unas\Laravel\Support\ClientManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $defaultConfig;

    /**
     * ClientFactory constructor.
     *
     * @param array                                         $config
     * @param \SzuniSoft\Unas\Laravel\Support\ClientManager $manager
     */
    public function __construct(array $config, ClientManager $manager)
    {
        $this->rememberAllowed = $config['remember_clients'] ?? true;
        $this->manager = $manager;
        $this->defaultConfig = $config;
    }

    /**
     * @param array $config
     *
     * @param bool  $inherit
     *
     * @return \SzuniSoft\Unas\Internal\Client
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    protected function getClient(array $config, $inherit = true)
    {
        if ($inherit && $this->defaultConfig) {

            $config = array_merge(
                $this->defaultConfig,
                $config
            );
        }

        if (!$this->rememberAllowed) {
            return new Client($config);
        }

        if ($this->manager->hasClient($config)) {
            return $this->manager->getClient($config);
        }

        return $this->manager->addClient($config, new Client($config));
    }


    /**
     * Creates a new client based on the given configuration.
     * When NO configuration provided it fall back
     * on the default configurations.
     *
     * @param array $config
     *
     * @param bool  $inherit
     *
     * @return \SzuniSoft\Unas\Internal\Client
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    public function create(array $config, $inherit = true)
    {
        return $this->getClient($config, $inherit);
    }

}
