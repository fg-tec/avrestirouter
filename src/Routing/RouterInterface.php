<?php

namespace AvrestiRouter\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function get(string $uri, $action): Route;

    public function post(string $uri, $action): Route;

    public function put(string $uri, $action): Route;

    public function patch(string $uri, $action): Route;

    public function delete(string $uri, $action): Route;

    public function group(array $attributes, callable $callback);

    public function resolve(ServerRequestInterface $request): ResponseInterface;

    public function generateUrl(string $name, array $parameters = []): string;

}