<?php


namespace SzuniSoft\Unas\Internal;


use function dd;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SzuniSoft\Unas\Exceptions\AuthenticationException;
use SzuniSoft\Unas\Exceptions\EndpointBlacklistedException;
use SzuniSoft\Unas\Exceptions\EventException;
use SzuniSoft\Unas\Exceptions\InvalidConfigurationException;
use SzuniSoft\Unas\Exceptions\InvalidResponseException;
use SzuniSoft\Unas\Exceptions\PremiumAuthenticationException;
use SzuniSoft\Unas\Internal\Builders\GetProductBuilder;
use SzuniSoft\Unas\Internal\Builders\PayloadBuilder;
use SzuniSoft\Unas\Model\Product;
use function array_merge;
use function compact;
use function event;
use function in_array;
use function is_string;
use function preg_match;

class Client
{

    use Parser;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $base;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var Carbon
     */
    protected $tokenExpiresAt;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $isPremium = false;

    private $username;

    private $password;

    private $shopId;

    private $authCode;

    /**
     * Client constructor.
     *
     * @param array $config
     *
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    public function __construct(array $config)
    {
        $this->validateConfig($config);

        $this->key = $config['key'] ?? null;
        $this->base = $config['base_path'];
        $this->client = $this->makeClient();
        $this->config = $config;
        $this->isPremium = !!($config['key'] ?? false);

        if (!$this->isPremium) {
            $this->username = $config['username'];
            $this->password = $config['password'];
            $this->shopId = $config['shop_id'];
            $this->authCode = $config['auth_code'];
        }

    }

    /**
     * @param $eventClass
     *
     * @return bool
     */
    public function wantsEvent($eventClass)
    {
        return in_array($eventClass, $this->config['events'] ?? []);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \Illuminate\Support\Carbon
     */
    public function getTokenExpiresAt(): Carbon
    {
        return $this->tokenExpiresAt;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function makeClient()
    {
        return new \GuzzleHttp\Client();
    }

    /**
     * @param array $config
     *
     * @return mixed
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    protected function validateConfig(array $config)
    {
        $validator = Validator::make($config, [
            'key' => ['required_without:username'],
            'username' => ['required_without:key'],
            'password' => ['required_without:key'],
            'shop_id' => ['required_without:key'],
            'auth_code' => ['required_without:key'],
            'base_path' => ['required', 'url'],
        ]);

        if ($validator->fails()) {
            $this->error(new InvalidConfigurationException($validator, $this));
        }

        return $config;
    }

    /**
     * @param $content
     *
     * @return bool|int
     */
    protected function findOutContentLength($content)
    {
        return ini_get('mbstring.func_overload') ? mb_strlen($content, '8bit') : strlen($content);
    }

    /**
     * @param $exception
     *
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    protected function error($exception)
    {
        if ($exception instanceof EventException && $this->wantsEvent($exception->eventClass())) {
            event($exception->makeEvent());
        }
        else {
            throw $exception;
        }
    }

    /**
     * @param       $uri
     * @param array $body
     * @param array $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function sendRequest($uri, $body = [], $headers = [])
    {
        // Setup request payload.
        $options = compact('body');

        // Apply token.
        if ($this->token) {

            $options['headers'] = array_merge(
                ['Authorization' => "Bearer $this->token"],
                $headers
            );
        }

        // Perform request.
        return $this->client->post($uri, $options);
    }

    /**
     * @param             $uri
     * @param string|null $parsedContent
     * @param array       $body
     *
     * @param array       $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    protected function request($uri, string &$parsedContent = null, $body = [], $headers = [])
    {

        // First request or token expired already.
        if (
            // Using legacy authorization.
            !$this->isPremium ||
            // Using premium authorization.
            ($this->isPremium &&
                (!$this->token || $this->tokenExpiresAt->isPast())
            )
        ) {
            $this->authorize(false, $body, $headers);
        }

        // Send request and receive response.
        $response = $this->sendRequest($uri, $body, $headers);

        // Parse response.
        $parsedContent = $this->parse($response->getBody());

        // Check if it is blacklist error.
        if (is_string($parsedContent) &&
            preg_match('/^Too much ([a-zA-Z\s]+) query, IP is banned till ([0-9\s\.\:]+)!$/', $parsedContent, $matches)) {

            $this->error(
                new EndpointBlacklistedException(
                    $matches[1],
                    Carbon::createFromFormat('m.d.Y H:i:s', $matches[2]),
                    $this
                )
            );
        }

        return $response;
    }

    /**
     * @param       $raw
     *
     * @return array
     * @throws \SzuniSoft\Unas\Exceptions\EventException
     */
    protected function parse($raw)
    {
        $payload = $this->parsePayload($raw);

        if (!$this->isPremium &&
            is_string($payload) &&
            preg_match('/^Authentication Error: ([a-zA-Z]+)$/', $payload, $matches)
        ) {

            $this->error(
                new AuthenticationException(
                    $this,
                    Str::snake($matches[1])
                )
            );
        }

        return $payload;
    }

    /**
     * @param bool $silent
     *
     * @return bool
     */
    protected function premiumAuthorization(bool $silent = false)
    {
        // Perform login request.
        $rawResponse = $this
            ->sendRequest('login', PayloadBuilder::forPremiumAuthorization($this->key))
            ->getBody();

        // Intercept response.
        $payload = $this->parse($rawResponse);

        // Key cannot be used because tenant has no premium package.
        if (is_string($payload) && $payload == ApiSchema::PREMIUM_PACKAGE_ERROR_MESSAGE) {
            throw new PremiumAuthenticationException($this);
        }

        try {

            if ($payload['Status'] !== 'ok') {
                if (!$silent) {
                    throw new AuthenticationException($this);
                }
                return false;
            }

            $this->token = $payload['Token'];
            $this->tokenExpiresAt = Carbon::createFromFormat(ApiSchema::AUTH_DATE_TIME_FORMAT, $payload['Expire']);
        } catch (Exception $e) {

            if ($e instanceof AuthenticationException) {
                throw $e;
            }

            throw new InvalidResponseException($e);
        }

        return true;
    }

    /**
     * @param array $body
     *
     * @return bool
     */
    protected function legacyAuthorization(&$body = [])
    {

        // Apply legacy authorization credentials.
        $body['auth'] = PayloadBuilder::forLegacyAuthorization(
            $this->username, $this->password, $this->shopId, $this->authCode
        );

        return true;
    }

    /**
     * Tries authorization with credentials.
     *
     * @param bool  $silent
     *
     * @param array $body
     * @param array $headers
     *
     * @return bool
     */
    public function authorize($silent = false, &$body = [], &$headers = [])
    {
        return $this->isPremium
            ? $this->premiumAuthorization($silent)
            : $this->legacyAuthorization($body);
    }

    /**
     * @return \SzuniSoft\Unas\Internal\Builders\GetProductBuilder
     */
    public function getProducts()
    {
        return GetProductBuilder::make(function ($params) {

            // Perform request.
            $this->request(
                'getProduct',
                $payload,
                PayloadBuilder::forGetProduct($params)
            );

            // Convert response to OOP style.
            return Collection::wrap(Arr::wrap($payload['Product']))->map(function ($rawProduct) {
                return new Product($rawProduct);
            });
        });
    }

    /**
     * @param $id
     *
     * @return Product|null
     */
    public function getProduct($id)
    {
        return $this->getProducts()->id($id)->retrieve()->first();
    }
}
