<?php

namespace App\Lib;

use PDO;

class PDOFactory
{
    public static function create($db = 'default'): PDO
    {
        $config = include("./config/database.php");
        $config = $config[$db];

        $dbtype = $config['type'];
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $user = $config['user'];
        $password = $config['password'];

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        $charset = '';
        if ($dbtype == 'mysql') {
            $charset = 'charset=charset=utf8mb4';
        }

        $pdo = new PDO("$dbtype:host=$host;port=$port;dbname=$database;$charset", $user, $password, $options);

        return $pdo;
    }
}