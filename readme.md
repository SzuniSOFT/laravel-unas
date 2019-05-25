[![Build Status](https://travis-ci.org/SzuniSOFT/laravel-unas.svg?branch=master)](https://travis-ci.org/SzuniSOFT/laravel-unas)
[![Latest Stable Version](https://poser.pugx.org/szunisoft/laravel-unas/version)](https://packagist.org/packages/szunisoft/laravel-unas)
[![Total Downloads](https://poser.pugx.org/szunisoft/laravel-unas/downloads)](https://packagist.org/packages/szunisoft/laravel-unas)
[![License](https://poser.pugx.org/szunisoft/laravel-unas/license)](https://packagist.org/packages/szunisoft/laravel-unas)
![PHP from Packagist](https://img.shields.io/packagist/php-v/szunisoft/laravel-unas.svg?label=php%20version&style=flat-square)

# Laravel Unas

_About the package_

The package is not finished yet however will be maintained and developed continuously as our primary project which requires this extension will.

_Naming conventions_

At some point the official Unas naming convention is not that good or does not exist at all.
However the package is not trying to introduce custom naming conventions it will use the official
property and attribute names as they are described in the [documentation](https://shop.unas.hu/apidoc).

## API compatibility

The following table contains the related API endpoints. All the endpoints will be supported soon.

Endpoint | Support |
--- | --- |
getProduct | :heavy_check_mark:
setProduct | :x:
getOrder | :x:
setOrder | :x:
getStock | :x:
setStock | :x:
getCategory | :x:
setCategory | :x:
getNewsletter | :x:
setNewsletter | :x:

## Future features :star:

##### Configurable Rate Limiting
Will fire an event each time an endpoint is being exhausted based on the given configuration.

## Requirements :exclamation:
- php >= 7.2
- ext-xmlwriter
- ext-simplexml

## Install :arrow_forward:
```
composer require szunisoft/laravel-unas
```

## Laravel support :hand:

``>= 5.5``

## Configure :gear:

Export the configuration
```
php artisan vendor:publish --provider="SzuniSoft/Unas/Laravel/UnasServiceProvider" --tag=config
```

##### Authentication
Two authentication drivers are supported.

###### Legacy (default driver)
_You must set the driver to **legacy**._
 
For legacy authentication you will need the following credentials:
- username
- password
- shop_id
- auth_code

###### Key 
_You must set the driver to **key**._

In case of key authentication you will need to generate an API key 
inside the Unas portal.

##### Error handling principles

Some kind of exceptions can be turned into events rather than throwing them.
This is just a code architectural decision how a particular circumstance should be handled.

For instance we create a multi-tenancy project which will handle tons of different clients traffic behind the scenes.
It is more wise to consume and intercept errors as events since we'll be able to use multiple listeners on it and we can send notification to the tenant also.

  
In the other hand if you'll need a very specific and custom application for a single tenant then it 
would be more appropriate to intercept errors as exceptions because it is probably the core component of the entire application.

_By default all events are disabled and going to raise._

To adjust your needs your can freely add or remove events declared in the configuration file.

```php
<?php 
return [
    
    // ....
    
    'events' => [
    
        // Add or remove events here.
        SzuniSoft\Unas\Laravel\Events\EndpointBlacklisted::class
    
    ]
];
```

The following errors can be turned into events.

Exception | Event
--- | ---
EndpointBlacklistedException | EndpointBlacklisted
InvalidConfigurationException | InvalidConfiguration
AuthenticationException | Unauthenticated
PremiumAuthenticationException | Unauthenticated

##### A little talk about each exception

###### EndpointBlacklistedException
Thrown when an endpoint is being exhausted for a single client.
You can access the client and the until Carbon instance.

###### InvalidConfigurationException
Thrown in case of bad client configuration.

###### AuthenticationException
This exception will be thrown when legacy or key credentials are invalid.
You can access the field which was invalid.

###### PremiumAuthenticationException
Thrown when the desired authentication way is API key but related tenant has no Premium package. In this case tenant should use the legacy approach. 

###### TooHighChunkException
Thrown when an endpoint does not allow  the amount of given results per page.

##### Remembering clients
Sometimes you wish to have multiple clients. If you want clients to be remembered you can turn on this feature in the configuration.
Remembering client will avoid creating multiple clients for the same authentication credentials.

### Creating and accessing client
There are multiple ways to create clients.

###### Dependency Injection
_This method will use the credentials given in the configuration._
```php
<?php 
function anyFunction (SzuniSoft\Unas\Internal\Client $client) {
}
```

###### IoC Container
_This method will use the credentials given in the configuration._
```php
<?php
$client = app(SzuniSoft\Unas\Internal\Client::class);
$client = app('unas.client');
```


Sometimes you want to customize the client by yourself. For these kind of desires you can use the client Builder and client Factory.

###### Client Builder
```php
<?php
$builder = app(SzuniSoft\Unas\Laravel\Support\ClientBuilder::class);
$builder = app('unas.client.builder');

$client = $builder
    // Apply premium authentication API key manually.
    ->withPremium('SomeApiKey')
    // Apply legacy authentication credentials manually.
    ->withLegacy('Username', 'Password', 'ShopID', 'AuthCode')
    // Override default base path to access Unas.
    // You should never set base url manually except when url changed.
    ->basePath('http://new.domain')
    // Set events that should be fired instead throwing as exception.
    ->allowedEvents(...)
    // Set events that should not be fired. Instead exception should be thrown.
    ->disallowedEvents(...)
    // Finally retrieve the client.
    ->build();
```


###### Client Factory
```php
<?php
$factory = app(SzuniSoft\Unas\Laravel\Support\ClientFactory::class);
$factory = app('unas.client.factory');

$config = [
    // Custom client configurations..
];

$client = $factory->create($config);
```


### Get Products
[Official details](https://shop.unas.hu/apidoc/getProduct)
```php
<?php

/** @var SzuniSoft\Unas\Internal\Client $client */
$client->getProducts()
    ->statusBase(...)
    ->parent(...)
    ->id(...)
    ->sku(...)
    ->timeStart(...)
    ->timeEnd(...)
    ->dateStart(...)
    ->dateEnd(...)
    ->contentType(...)
    ->contentParam(...)
    ->chunk(function (Illuminate\Support\Collection $products) {
        
        $products->each(function (SzuniSoft\Unas\Model\Product $product) {
            // Process product.
        });
        
    }, $perPage = 50);
```

Accessing page #1 with 50 products per page.
```php
<?php

/** @var SzuniSoft\Unas\Internal\Client $client */
$client->getProducts()
    ->paginate(50)
    ->page(1);
```
