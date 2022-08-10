<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Request;
use Exception;
use PDO;

class SaleController extends Controller
{
    public function create(Request $request)
    {
        $response = ['ok' => false];

        $products = $request->getParameter('products');

        $this->pdo->beginTransaction();

        try {
            if (count($products) > 0) {
                $insert_sale = $this->pdo->prepare(
                    "INSERT INTO 
                        sales
                        (date,status)
                    VALUES
                        (NOW(),1)"
                );
                $insert_sale->execute();

                $sale_id = $this->pdo->lastInsertId();

                $sale_product_controller = new SaleProductController($this->pdo);

                $total_price = 0;
                $total_tax = 0;

                foreach ($products as $key => $product_info) {
                    $product_id = $product_info['id'];
                    $product_quantity = $product_info['quantity'];

                    $response_spc = $sale_product_controller->create($sale_id, $product_id, $product_quantity);
                    $response_spc = json_decode($response_spc);

                    $total_price += $response_spc->product_total_price;
                    $total_tax += $response_spc->product_total_tax;
                }

                $update_sale = $this->pdo->prepare(
                    "UPDATE 
                        sales 
                    SET 
                        total_price = :total_price, 
                        total_tax = :total_tax 
                    WHERE 
                        id = :id"
                );
                $update_sale->bindParam(':id', $sale_id);
                $update_sale->bindParam(':total_price', $total_price);
                $update_sale->bindParam(':total_tax', $total_tax);

                $update_sale->execute();

                $this->pdo->commit();

                $response['ok'] = true;
            } else {
                throw new Exception('Não há produtos selecionados!');
            }
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return json_encode($response);
    }

    public function get($id)
    {
        $query = $this->pdo->prepare(
            "SELECT * FROM sales WHERE id = :id"
        );

        $query->bindParam(':id', $id);

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetch(PDO::FETCH_ASSOC);

        return json_encode($response);
    }

    public function list()
    {
        $query = $this->pdo->prepare(
            "SELECT * FROM sales WHERE status = 1 ORDER BY date DESC"
        );

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetchAll();

        return json_encode($response);
    }
}