<?php

namespace AvrestiRouter\Routing\Facade;

use AvrestiRouter\Routing\AvrestiRouter;
use Exception;

/**
 * Class Route
 *
 * Provides a static interface for the Router class.
 *
 * @method static \AvrestiRouter\Routing\Route get(string $uri, $action)
 * @method static \AvrestiRouter\Routing\Route post(string $uri, $action)
 * @method static \AvrestiRouter\Routing\Route put(string $uri, $action)
 * @method static \AvrestiRouter\Routing\Route patch(string $uri, $action)
 * @method static \AvrestiRouter\Routing\Route delete(string $uri, $action)
 * @method static void group(array $attributes, callable $callback)
 * @method static \Psr\Http\Message\ResponseInterface resolve(\Psr\Http\Message\ServerRequestInterface $request)
 * @method static string generateUrl(string $name, array $parameters = [])
 * @method static \AvrestiRouter\Routing\Route|null getCurrentRoute()
 */
class Route
{
    /**
     * @var AvrestiRouter
     */
    protected static AvrestiRouter $router;

    /**
     * Sets the router instance.
     *
     * @param AvrestiRouter $router
     */
    public static function setRouter(AvrestiRouter $router): void
    {
        self::$router = $router;
    }

    /**
     * Magic method to call methods on the router instance.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic(string $method, array $arguments)
    {
        if (!self::$router) {
            throw new Exception('Router not set');
        }
        return call_user_func_array([self::$router, $method], $arguments);
    }
}
