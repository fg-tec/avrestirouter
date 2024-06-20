<?php

namespace AvrestiRouter\Routing;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AvrestiRouter
 *
 * Manages the registration, grouping, and resolution of routes.
 */
class AvrestiRouter implements RouterInterface
{
    /**
     * @var array The array of registered routes.
     */
    private array $routes = [];

    /**
     * @var array The array of named routes.
     */
    private array $namedRoutes = [];

    /**
     * @var array The current group attributes.
     */
    private array $currentGroupAttributes = [];

    /**
     * @var Route|null The current matched route.
     */
    private ?Route $currentRoute = null;

    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a PATCH route.
     *
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function patch(string $uri, mixed $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Registers a group of routes.
     *
     * @param array $attributes
     * @param callable $callback
     * @return void
     */
    public function group(array $attributes, callable $callback): void
    {
        $previousGroupAttributes = $this->currentGroupAttributes;
        $this->currentGroupAttributes = array_merge($this->currentGroupAttributes, $attributes);
        $callback($this);
        $this->currentGroupAttributes = $previousGroupAttributes;
    }

    /**
     * Resolves the incoming request to the appropriate route.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function resolve(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();

        foreach ($this->routes as $route) {
            if ($route->method === $method && $this->matchUri($route->uri, $uri, $parameters)) {
                $route->setParameters($parameters);
                $this->currentRoute = $route;
                return $this->handleRouteAction($route);
            }
        }

        throw new Exception('Route not found');
    }

    /**
     * Generates a URL for a named route.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws Exception
     */
    public function generateUrl(string $name, array $parameters = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new Exception("Route with name {$name} not found");
        }

        $route = $this->namedRoutes[$name];
        $url = $route->uri;

        foreach ($parameters as $key => $value) {
            $url = str_replace("{{$key}}", $value, $url);
        }

        return $url;
    }

    /**
     * Adds a named route.
     *
     * @param Route $route
     */
    public function addNamedRoute(Route $route): void
    {
        if ($route->name) {
            $this->namedRoutes[$route->name] = $route;
        }
    }

    /**
     * Returns the current matched route.
     *
     * @return Route|null
     */
    public function getCurrentRoute(): ?Route
    {
        return $this->currentRoute;
    }

    /**
     * Adds a route to the router.
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     * @param string|null $name
     * @return Route
     */
    private function addRoute(string $method, string $uri, mixed $action, ?string $name = null): Route
    {
        $groupName = $this->currentGroupAttributes['group'] ?? null;
        $route = new Route($method, $uri, $action, $groupName);

        if ($name) {
            $route->name = $name;
            $this->namedRoutes[$name] = $route;
        }

        $this->routes[] = $route;
        return $route;
    }

    /**
     * Matches the request URI against the route URI.
     *
     * @param string $routeUri
     * @param string $requestUri
     * @param array|null $parameters
     * @return bool
     */
    private function matchUri(string $routeUri, string $requestUri, ?array &$parameters): bool
    {
        $routeUriPattern = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([a-zA-Z0-9_-]+)', $routeUri);
        $routeUriPattern = str_replace('/', '\/', $routeUriPattern);
        if (preg_match('/^' . $routeUriPattern . '$/', $requestUri, $matches)) {
            array_shift($matches);
            $parameters = $matches;
            return true;
        }
        return false;
    }

    /**
     * Handles the action for a matched route.
     *
     * @param Route $route
     * @return ResponseInterface
     * @throws Exception
     */
    private function handleRouteAction(Route $route): ResponseInterface
    {
        $parameters = $route->parameters;

        if (is_callable($route->action)) {
            return call_user_func_array($route->action, $parameters);
        } elseif (is_array($route->action)) {
            $controller = new $route->action[0];
            return call_user_func_array([$controller, $route->action[1]], $parameters);
        } else {
            throw new Exception('Invalid route action');
        }
    }
}
