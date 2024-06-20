<?php

namespace AvrestiRouter\Routing;

/**
 * Class Route
 *
 * Represents a single route in the application
 */
class Route
{
    public string $method;

    public string $uri;

    public mixed $action;

    public ?string $name = null;

    public ?string $group;

    public array $parameters = [];

    /**
     * Route constructor
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     * @param string|null $group
     */
    public function __construct(string $method, string $uri, mixed $action, ?string $group = null)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->group = $group;
    }

    /**
     * Sets the name of the route.
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name): static
    {
        $this->name = $name;
        global $router;

        $router->addNamedRoute($this);
        return $this;
    }

    /**
     * Sets the parameters for the route.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the name of the route.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the parameters of the route.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the group of the route.
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getUrl(array $parameters = []): array|string
    {
        $url = $this->uri;

        foreach ($parameters as $key => $value) {
            $url = str_replace("{{$key}}", $value, $url);
        }
        return $url;
    }

}