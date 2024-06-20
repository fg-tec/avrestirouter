<?php

require "../vendor/autoload.php";
require "TestController.php";

use AvrestiRouter\Routing\AvrestiRouter;
use AvrestiRouter\Routing\Facade\Route;
use GuzzleHttp\Psr7\ServerRequest;

if (getenv('ENV') !== 'production') {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// Create router instance
$router = new AvrestiRouter();
Route::setRouter($router);

// Define an unnamed route outside any group
Route::get('/contact', function () {
    return new \GuzzleHttp\Psr7\Response(200, [], "<h1>Contact page</h1> <br>");
});

// Define anamed route outside any group
Route::get('/about', function () {
    return new \GuzzleHttp\Psr7\Response(200, [], "<h1>Contact page</h1> <br>");
})->name('about');

// Define routes with a group
Route::group(['group' => 'auth'], function () {
    Route::get('/', function () {
        return new \GuzzleHttp\Psr7\Response(200, [], "Hello World");
    })->name('index');

    Route::get('/test', [\TestController::class, 'create'])->name('test');
    Route::get('/profile/{id}', function ($id) {
        return new \GuzzleHttp\Psr7\Response(200, [], "Profile ID: $id <br>");
    })->name('profile.show');
});

// Create a server request instance
$request = ServerRequest::fromGlobals();

// Resolve the request
try {
    $response = Route::resolve($request);
    // Output the response body
    echo $response->getBody();
} catch (Exception $e) {
    echo $e->getMessage();
}

// Access the current route
$currentRoute = Route::getCurrentRoute();
if ($currentRoute) {
    echo "Current route name: " . $currentRoute->getName() . "<br>";
    echo "Current route parameters: " . json_encode($currentRoute->getParams()) . "<br>";
    echo "Current route group: " . $currentRoute->getGroup() . "<br>";
}

// Generate URL from named route
try {
    echo "<br>" . Route::generateUrl('profile.show', ['id' => 123]); // Output: /profile/123
} catch (Exception $e) {
    echo $e->getMessage();
}