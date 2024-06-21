# AvrestiRouter
[![PHP Composer](https://github.com/fg-tec/avrestirouter/actions/workflows/php.yml/badge.svg)](https://github.com/fg-tec/avrestirouter/actions/workflows/php.yml)

AvrestiRouter is a simple yet powerful PHP routing library inspired by Laravel. It provides an easy-to-use API for defining and resolving routes, handling different HTTP methods, and managing route groups with named parameters.

## Features
* Support for all HTTP methods (GET, POST, PUT, PATCH, DELETE)
* Named route parameters for dynamic routing
* Grouped routes for better organization
* Custom regex patterns for route parameters

## Requirements
* PHP >= 8.3

## Installation
You can install AvrestiRouter using Composer:

```bash
composer require franco2911/avrestirouter
```

## Getting Started

### Initialize AvrestiRouter

First, initialize the `AvrestiRouter` and set it using the `Route` facade:

```php
require 'vendor/autoload.php';

use AvrestiRouter\Routing\AvrestiRouter;
use AvrestiRouter\Routing\Facade\Route;

$router = new AvrestiRouter();
Route::setRouter($router);
```

### Defining Routes

You can define routes using the `Route` facade. Below are examples for various route definitions:

#### Basic Route

```php
Route::get('/contact', function () {
    return new \GuzzleHttp\Psr7\Response(200, [], "<h1>Contact page</h1>");
});
```

#### Named Route

```php
Route::get('/about', function () {
    return new \GuzzleHttp\Psr7\Response(200, [], "<h1>About page</h1>");
})->name('about');
```

#### Grouped Routes

```php
Route::group(['group' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return new \GuzzleHttp\Psr7\Response(200, [], "Dashboard");
    })->name('dashboard');

    Route::get('/settings', [\TestController::class, 'create'])->name('settings');
    
    Route::get('/profile/{id}', function ($id) {
        return new \GuzzleHttp\Psr7\Response(200, [], "<h1>Profile ID: $id</h1>");
    })->name('profile.show');
});
```

### Resolving Routes

To resolve the current route based on the incoming request, use the `resolve` method. You can create a `ServerRequest` instance using Guzzle:

```php
use GuzzleHttp\Psr7\ServerRequest;

$request = ServerRequest::fromGlobals();
$response = Route::resolve($request);

// Output the response body
echo $response->getBody();
```

### Generating URLs from Route Names

You can generate URLs for named routes using the `generateUrl` method:

```php
echo Route::generateUrl('profile.show', ['id' => 1259]); // Output: /profile/1259
```

## Example Usage

You can find a complete example demonstrating the usage of AvrestiRouter in the example directory.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE.md) file for details.

## Contributions

Contributions are welcome! Please fork this repository and submit pull requests to contribute improvements or new features.
