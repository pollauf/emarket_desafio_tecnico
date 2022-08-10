<?php

use App\Controllers\ProductController;
use App\Controllers\ProductTypeController;
use App\Controllers\SaleController;
use App\Controllers\SaleProductController;
use App\Controllers\UserController;
use App\Lib\Router;

$router = new Router();

$router->get('/', function () {
    return 'API';
});

$router->post('/user/login', [UserController::class, 'login']);

$router->setMiddleware(function ($pdo) {
    $headers = apache_request_headers();

    $token = isset($headers['token']) ? $headers['token'] : null;

    if (empty($token)) {
        $token = isset($headers['Token']) ? $headers['Token'] : null;
    }

    $query_user = $pdo->prepare("SELECT id FROM users WHERE token = :token");
    $query_user->bindParam(':token', $token);
    $query_user->execute();

    $authorized = $query_user->rowCount() > 0;

    return $authorized;
}, function ($router) {
    $router->post('/product/store', [ProductController::class, 'store']);
    $router->get('/product/disable/{id}', [ProductController::class, 'disable']);
    $router->get('/product/get/{id}', [ProductController::class, 'get']);
    $router->get('/product/list/{only_stock_available}', [ProductController::class, 'list']);

    $router->post('/product/type/store', [ProductTypeController::class, 'store']);
    $router->get('/product/type/disable/{id}', [ProductTypeController::class, 'disable']);
    $router->get('/product/type/get/{id}', [ProductTypeController::class, 'get']);
    $router->get('/product/type/list', [ProductTypeController::class, 'list']);

    $router->post('/sale/create', [SaleController::class, 'create']);
    $router->get('/sale/get/{id}', [SaleController::class, 'get']);
    $router->get('/sale/list', [SaleController::class, 'list']);

    $router->get('/sale/product/list/{sale_id}', [SaleProductController::class, 'list']);
});