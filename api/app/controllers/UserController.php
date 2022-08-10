<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Request;
use PDO;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $response = [
            'ok' => false,
            'token' => '',
            'msg' => 'Credenciais incorretas!',
        ];

        if ($request->validateParameters(['login', 'password'])) {
            $login = $request->getParameter('login');
            $password = md5($request->getParameter('password'));

            $query = $this->pdo->prepare(
                "SELECT 
                    token 
                FROM 
                    users 
                WHERE 
                    login = :login 
                AND 
                    password = :password"
            );
            $query->bindParam(':login', $login);
            $query->bindParam(':password', $password);

            $query->execute();

            if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $response['ok'] = true;
                $response['token'] = $row['token'];
                $response['msg'] = 'Sucesso!';
            }
        }

        return json_encode($response);
    }
}