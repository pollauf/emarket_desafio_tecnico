<?php

namespace App\Lib;

class Route
{
    private $action;
    private $middleware;

    public function __construct($action, $middleware = null)
    {
        $this->action = $action;
        $this->middleware = $middleware;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getMiddleware()
    {
        return $this->middleware;
    }
}