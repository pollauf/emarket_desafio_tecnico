<?php

namespace App\Controllers;

use App\Lib\Controller;
use PDO;

class SaleProductController extends Controller
{
    public function create($sale_id, $product_id, $product_quantity)
    {
        $response = [
            'ok' => false,
        ];

        $product_controller = new ProductController($this->pdo);
        $product_type_controller = new ProductTypeController($this->pdo);

        $product = json_decode($product_controller->get($product_id))->data;

        $product_type_id = $product->id_product_type;
        $product_price = floatval($product->price);

        $product_type = json_decode($product_type_controller->get($product_type_id))->data;

        $product_tax = $product_price * (floatval($product_type->tax) / 100);

        $product_total_price = $product_price * $product_quantity;
        $product_total_tax = $product_tax * $product_quantity;

        $insert_sale_product = $this->pdo->prepare(
            "INSERT INTO 
                sale_products
                (id_sale,id_product,unit_price,unit_tax,quantity,total_price,total_tax) 
            VALUES 
                (:id_sale,:id_product,:unit_price,:unit_tax,:quantity,:total_price,:total_tax)"
        );
        $insert_sale_product->bindParam(':id_sale', $sale_id);
        $insert_sale_product->bindParam(':id_product', $product_id);
        $insert_sale_product->bindParam(':unit_price', $product_price);
        $insert_sale_product->bindParam(':unit_tax', $product_tax);
        $insert_sale_product->bindParam(':quantity', $product_quantity);
        $insert_sale_product->bindParam(':total_price', $product_total_price);
        $insert_sale_product->bindParam(':total_tax', $product_total_tax);

        $success = $insert_sale_product->execute();

        if ($success) {
            $product_controller->subtractStock($product_id, $product_quantity);
        }

        $response['ok'] = $success;
        $response['product_total_price'] = $product_total_price;
        $response['product_total_tax'] = $product_total_tax;

        return json_encode($response);
    }

    public function list($sale_id)
    {
        $query = $this->pdo->prepare(
            "SELECT 
                s.id, s.id_product, s.unit_price, s.unit_tax, 
                s.quantity, s.total_price, s.total_tax,
                p.name, p.reference
            FROM 
                sale_products s
            INNER JOIN 
                products p 
            ON 
                p.id = s.id_product
            WHERE 
                s.id_sale = :id_sale"
        );

        $query->bindParam(':id_sale', $sale_id);

        $response['ok'] = $query->execute();
        $response['data'] = $query->fetchAll();

        return json_encode($response);
    }
}