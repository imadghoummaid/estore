<?php
namespace PHPMVC\Lib;

class Router
{
    private array $_routes = [];

    public function add(string $route, array $params = []): void
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters and case-insensitive flag
        $route = '/^' . $route . '$/i';

        $this->_routes[$route] = $params;
    }

    public function match(string $url): array|bool
    {
        foreach ($this->_routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                return $params;
            }
        }
        return false;
    }
}
