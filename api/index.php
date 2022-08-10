<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding, token");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require('./autoloader.php');

use App\Lib\URL;
use App\Lib\PDOFactory;
use App\Lib\Request;

$url = URL::getURL();

$pdo = PDOFactory::create();

try {
    $request_method = $_SERVER['REQUEST_METHOD'];

    require('./routes.php');

    if ($route = $router->handler($url, $request_method)) {
        $middleware = $route->getMiddleware();

        if (!empty($middleware) && !$middleware($pdo)) {
            throw new Exception('Não autorizado!');
        }

        $action = $route->getAction();

        if (is_array($action) && count($action) == 2) {
            $controller = new $action[0]($pdo);
            $method = $action[1];

            if ($request_method == 'GET') {
                $parameters = $router->getParameters();
                $response = $controller->$method(...$parameters);
            } else if ($request_method == 'POST') {
                $post = json_decode(file_get_contents('php://input'), true);;
                $response = $controller->$method(new Request($post));
            }
        } else if (is_callable($action)) {
            $response = $action();
        }

        echo $response;
    } else {
        throw new Exception('Rota não encontrada!');
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}