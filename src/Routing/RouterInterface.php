<?php

namespace AvrestiRouter\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RouterInterface
 *
 * Defines the methods that any Router class must implement.
 */

interface RouterInterface
{
    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function get(string $uri, $action): Route;

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function post(string $uri, $action): Route;

    /**
     * Register a PUT route.
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function put(string $uri, $action): Route;

    /**
     * Register a PATCH route
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function patch(string $uri, $action): Route;

    /**
     * Register a DELETE route
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function delete(string $uri, $action): Route;

    /**
     * Register a group of routes
     *
     * @param array $attributes
     * @param callable $callback
     * @return mixed
     */
    public function group(array $attributes, callable $callback): void;

    /**
     * Resolves the incoming request to the appropriate route
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function resolve(ServerRequestInterface $request): ResponseInterface;

    /**
     * Generates a URL for a named route
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function generateUrl(string $name, array $parameters = []): string;

}