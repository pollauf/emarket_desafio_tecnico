<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Request;
use PDO;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $response = ['ok' => false];

        $request->validateParameters(
            ['id_product_type', 'name', 'reference', 'price', 'stock_amount']
        );

        $id = intval($request->getParameter('id'));
        $id_product_type = intval($request->getParameter('id_product_type'));
        $name = $request->getParameter('name');
        $reference = $request->getParameter('reference');
        $price = floatval($request->getParameter('price'));
        $stock_amount = intval($request->getParameter('stock_amount'));

        $query_validate = $this->pdo->prepare("SELECT id FROM products WHERE reference = :reference AND id != :id AND status = 1");
        $query_validate->bindParam(':reference', $reference);
        $query_validate->bindParam(':id', $id);

        $query_validate->execute();

        $valid = $query_validate->rowCount() <= 0;

        if (!$valid) {
            $response['msg'] = 'Já existe um produto com essa referência!';
        } else {
            $edit_mode = $id > 0;

            if (!$edit_mode) {
                $query = $this->pdo->prepare(
                    "INSERT INTO 
                        products
                        (id_product_type,name,reference,price,stock_amount,status) 
                    VALUES 
                        (:id_product_type,:name,:reference,:price,:stock_amount,1)"
                );
            } else {
                $query = $this->pdo->prepare(
                    "UPDATE 
                        products 
                    SET 
                        id_product_type = :id_product_type,
                        name = :name,
                        reference = :reference,
                        price = :price,
                        stock_amount = :stock_amount 
                    WHERE 
                        id = :id"
                );

                $query->bindParam(':id', $id);
            }

            $query->bindParam(':id_product_type', $id_product_type);
            $query->bindParam(':name', $name);
            $query->bindParam(':reference', $reference);
            $query->bindParam(':price', $price);
            $query->bindParam(':stock_amount', $stock_amount);

            $response['ok'] = $query->execute();
        }

        return json_encode($response);
    }

    public function disable($id)
    {
        $query = $this->pdo->prepare(
            "UPDATE products SET status = 0 WHERE id = :id"
        );

        $query->bindParam(':id', $id);

        $response['ok'] = $query->execute();

        return json_encode($response);
    }

    public function get($id)
    {
        $query = $this->pdo->prepare(
            "SELECT * FROM products WHERE id = :id"
        );

        $query->bindParam(':id', $id);

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetch(PDO::FETCH_ASSOC);

        return json_encode($response);
    }

    public function list($only_stock_available)
    {
        $extra_query = '';

        if ($only_stock_available) {
            $extra_query = 'AND p.stock_amount > 0';
        }

        $query = $this->pdo->prepare(
            "SELECT 
                p.id, p.name, p.reference, p.id_product_type, p.price,
                p.stock_amount, t.tax, t.name AS name_product_type
            FROM 
                products p 
            INNER JOIN 
                product_types t 
            ON 
                t.id = p.id_product_type
            WHERE 
                p.status = 1 
            $extra_query"
        );

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetchAll();

        return json_encode($response);
    }

    public function subtractStock($product_id, $quantity)
    {
        $response = ['ok' => false];

        $query = $this->pdo->prepare(
            "UPDATE 
                products 
            SET 
                stock_amount = stock_amount - :quantity 
            WHERE 
                id = :id"
        );

        $query->bindParam(':id', $product_id);
        $query->bindParam(':quantity', $quantity);

        $response['ok'] = $query->execute();

        return json_encode($response);
    }
}