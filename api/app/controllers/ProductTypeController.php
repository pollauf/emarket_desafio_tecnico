<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Request;
use PDO;

class ProductTypeController extends Controller
{
    public function store(Request $request)
    {
        $response = ['ok' => false];

        $request->validateParameters(
            ['name', 'tax']
        );

        $id = intval($request->getParameter('id'));
        $name = $request->getParameter('name');
        $tax = $request->getParameter('tax');

        $query_validate = $this->pdo->prepare("SELECT id FROM product_types WHERE name = :name AND id != :id AND status = 1");
        $query_validate->bindParam(':name', $name);
        $query_validate->bindParam(':id', $id);

        $query_validate->execute();

        $valid = $query_validate->rowCount() <= 0;

        if (!$valid) {
            $response['msg'] = 'Já existe um tipo de produto com esse nome!';
        } else {
            $edit_mode = $id > 0;

            if (!$edit_mode) {
                $query = $this->pdo->prepare(
                    "INSERT INTO 
                        product_types
                        (name,tax,status) 
                    VALUES 
                        (:name,:tax,1)"
                );
            } else {
                $query = $this->pdo->prepare(
                    "UPDATE 
                        product_types 
                    SET 
                        name = :name,
                        tax = :tax
                    WHERE 
                        id = :id"
                );

                $query->bindParam(':id', $id);
            }

            $query->bindParam(':name', $name);
            $query->bindParam(':tax', $tax);

            $response['ok'] = $query->execute();
        }

        return json_encode($response);
    }

    public function disable($id)
    {
        $query = $this->pdo->prepare(
            "UPDATE product_types SET status = 0 WHERE id = :id"
        );

        $query->bindParam(':id', $id);

        $response['ok'] = $query->execute();

        return json_encode($response);
    }

    public function get($id)
    {
        $query = $this->pdo->prepare(
            "SELECT * FROM product_types WHERE id = :id"
        );

        $query->bindParam(':id', $id);

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetch(PDO::FETCH_ASSOC);

        return json_encode($response);
    }

    public function list()
    {
        $query = $this->pdo->prepare(
            "SELECT * FROM product_types WHERE status = 1"
        );

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetchAll();

        return json_encode($response);
    }
}