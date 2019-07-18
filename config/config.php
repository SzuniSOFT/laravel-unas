<?php

return [

    // Authentication driver.
    // Possible values:
    // - legacy
    // - key
    //
    'driver' => env('UNAS_DRIVER', 'legacy'),

    // Authentication for driver [legacy]
    'username' => env('UNAS_API_USERNAME'),
    'password' => env('UNAS_API_PASSWORD'),
    'shop_id' => env('UNAS_API_SHOP_ID'),
    'auth_code' => env('UNAS_API_AUTH_CODE'),

    // Authentication for driver [key]
    // To use api keys you must have at least
    // PREMIUM package and create an API key.
    //
    // If you don't have PREMIUM package please
    // use the legacy driver instead!
    'key' => env('UNAS_API_KEY'),

    // Changing this is not recommended.
    'base_path' => env('UNAS_BASE_PATH', 'https://api.unas.eu/shop/'),

    // Set to true to debug Guzzle client
    'debug' => false,

    // GuzzleHttp timeouts
    'timeouts' => [

        // @see http://docs.guzzlephp.org/en/stable/request-options.html#connect-timeout
        'connect_timeout' => 30,
        // @see http://docs.guzzlephp.org/en/stable/request-options.html#timeout
        'timeout' => 120,
        // @see http://docs.guzzlephp.org/en/stable/request-options.html#read-timeout
        'read_timeout' => 120,
    ],

    'events' => [

        // Add or remove events here.
        //SzuniSoft\Unas\Laravel\Events\EndpointBlacklisted::class,
        //SzuniSoft\Unas\Laravel\Events\InvalidConfiguration::class,
        //SzuniSoft\Unas\Laravel\Events\Unauthenticated::class,

    ],

    'global' => [

        // During the application lifecycle it can be handy
        // making clients to be remembered.
        // By turning this feature on the client duplications
        // can be detected based on their credentials.
        //
        // For instance when you try to create a client with the same
        // credentials the existing instance will be resolved instead.
        // Turn this feature off if you want new clients always.
        'remember_clients' => env('UNAS_REMEMBER_CLIENTS', true),
    ],

];
