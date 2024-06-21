<?php

namespace AvrestiRouter\Routing;

/**
 * Class Route
 *
 * Represents a single route in the application.
 */
class Route
{
    private AvrestiRouter $router;
    public string $method;
    public string $uri;
    public mixed $action;
    public ?string $name;
    public ?string $group;
    public array $parameters = [];

    /**
     * Route constructor.
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     * @param string|null $group
     * @param AvrestiRouter $router
     */
    public function __construct(string $method, string $uri, mixed $action, ?string $group, AvrestiRouter $router)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->name = null;
        $this->group = $group;
        $this->router = $router;
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
        $this->router->addNamedRoute($this);
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

    /**
     * Returns the method of the route.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the URI of the route.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Returns the action of the route.
     *
     * @return mixed
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * Returns the URL for the route with replaced parameters.
     *
     * @param array $parameters
     * @return string
     */
    public function getUrl(array $parameters = []): string
    {
        $url = $this->uri;

        foreach ($parameters as $key => $value) {
            $url = str_replace("{{$key}}", $value, $url);
        }
        return $url;
    }
}
