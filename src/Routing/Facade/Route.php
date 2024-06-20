<?php

namespace AvrestiRouter\Routing\Facade;

use AvrestiRouter\Routing\AvrestiRouter;
use Exception;

/**
 * Class Route
 *
 * Provide a static interface for the Router class.
 */
class Route
{
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
        if (! self::$router) {
            throw new Exception('Router not set');
        }

        return call_user_func_array([self::$router, $method], $arguments);
    }

    /**
     * Generates a URL for a named route.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws Exception
     */
    public static function generateUrl(string $name, array $parameters = []): string
    {
        if (!self::$router) {
            throw new Exception('Router not set');
        }
        return self::$router->generateUrl($name, $parameters);
    }

    /**
     * Returns the current matched route.
     *
     * @return \AvrestiRouter\Routing\Route|null
     * @throws Exception
     */
    public static function getCurrentRoute(): ?\AvrestiRouter\Routing\Route
    {
        if (!self::$router) {
            throw new \Exception('Router not set');
        }
        return self::$router->getCurrentRoute();
    }
}
