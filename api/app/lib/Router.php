<?php

namespace App\Lib;

class Router
{
    private $routes;
    private $parameters;
    private $middleware;

    public function __construct()
    {
        $this->routes = [];
        $this->parameters = [];
        $this->middleware = null;
    }

    public function setMiddleware($middleware, $function)
    {
        $this->middleware = $middleware;

        $function($this);

        $this->middleware = null;
    }

    public function get($route, $action)
    {
        $this->createRoute('GET', $route, $action);
    }

    public function post($route, $action)
    {
        $this->createRoute('POST', $route, $action);
    }

    private function createRoute($method, $route, $action)
    {
        $this->routes[$method][$route] = new Route($action, $this->middleware);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function handler($url, $method)
    {
        if (empty($this->routes[$method])) {
            return false;
        }

        if (isset($this->routes[$method][$url])) {
            return $this->routes[$method][$url];
        }

        foreach ($this->routes[$method] as $route => $action) {
            $result = $this->matchURL($route, $url);
            if ($result >= 1) {
                return $action;
            }
        }

        return false;
    }

    private function matchURL($route, $url)
    {
        preg_match_all('/\{([^\}]*)\}/', $route, $variables);

        $regex = str_replace('/', '\/', $route);

        foreach ($variables[0] as $k => $variable) {
            $replacement = '([a-zA-Z0-9\-\_\ ]+)';
            $regex = str_replace($variable, $replacement, $regex);
        }

        $regex = preg_replace('/{([a-zA-Z]+)}/', '([a-zA-Z0-9+])', $regex);
        $result = preg_match('/^' . $regex . '$/', $url, $parameters);

        if (count($parameters) > 0) {
            array_shift($parameters);
        }

        $this->parameters = $parameters;

        return $result;
    }
}