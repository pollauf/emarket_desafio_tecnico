<?php

namespace App\Lib;

use Exception;

class Request
{
    public $parameters;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return '';
    }

    public function validateParameters($parameters)
    {
        foreach ($parameters as $key => &$parameter) {
            if (!isset($this->parameters[$parameter]) || $this->parameters[$parameter] === '') {
                throw new Exception($parameter . ' obrigatório!');
                return false;
            }
        }

        return true;
    }
}