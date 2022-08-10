<?php

namespace App\Lib;

abstract class Controller
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
}