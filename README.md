# OpenPix PHP SDK

[![PHP version](https://img.shields.io/packagist/php-v/open-pix/php-sdk?color=%2325c2a0&logo=php&label=open-pix/php-sdk)](https://packagist.org/packages/open-pix/php-sdk)
[![Download stats](https://img.shields.io/packagist/dt/open-pix/php-sdk?color=%2325c2a0&logo=php)](https://packagist.org/packages/open-pix/php-sdk)
[![Latest release](https://img.shields.io/packagist/v/open-pix/php-sdk?label=latest+version)](https://packagist.org/packages/open-pix/php-sdk)
[![CI status](https://img.shields.io/github/actions/workflow/status/Open-Pix/php-sdk/code-quality.yml?branch=main&label=tests&logo=github)](https://packagist.org/packages/open-pix/php-sdk)

Welcome to the OpenPix PHP SDK! This SDK provides convenient access to the OpenPix REST API, allowing you to easily integrate payment services into your PHP applications.

<p align="center">
  <img src="example.png" alt="usage example screenshot" width="700">
</p>

## Documentation

See [SDK documentation](https://developers.openpix.com.br/docs/sdk/php/sdk-php-what-is) and [Rest API documentation](https://developers.openpix.com.br/api).

## Installation

Install the SDK with the necessary dependencies using Composer:

```bash
$ composer require open-pix/php-sdk guzzlehttp/guzzle guzzlehttp/psr7
```

## Basic usage

Here is the basic usage of the SDK. See [SDK documentation](https://developers.openpix.com.br/docs/sdk/php/sdk-php-usage) for more details.

```php
use OpenPix\PhpSdk\Client;

// Load autoload of Composer.
require_once __DIR__ . "/vendor/autoload.php";

$client = Client::create("YOUR_APP_ID");

// Create a customer.
$customer = [
    "name" => "Dan PHP-SDK",
    "taxID" => "00000000000", // CPF
    "email" => "email0@example.com",
    "phone" => "5511999999999",
    "correlationID" => "test-php-sdk-customer-" . mt_rand(1, 10000),
];

$client->customers()->create($customer);

// Create a charge using above customer.
$charge = [
    // Charge value.
    "value" => 1000, // (R$ 10,00)

    // Your correlation ID to keep track of this charge.
    "correlationID" => "test-php-sdk-charge-" . mt_rand(1, 10000),

    // Charge customer.
    "customer" => $customer,
];

$result = $client->charges()->create($charge);

// Get the generated dynamic BR code to be rendered as a QR Code.
echo $result["brCode"] . "\n";
```

## Contributing

If you have suggestions for how OpenPix PHP SDK could be improved, or want to report a bug, open an issue! We'd love all and any contributions.

For more, check out the [Contributing Guide](CONTRIBUTING.md).

## License

OpenPix PHP SDK is distributed under the terms of the [MIT license](LICENSE).
