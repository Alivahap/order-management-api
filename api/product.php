<?php
// /api/product.php

require_once '../config/database.php';
require_once '../models/Product.php';

$product = new Product($pdo);

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($product->getAll());
        break;

    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            echo json_encode($product->getById($id));
        } else {
            echo json_encode(['error' => 'Ürün ID belirtilmedi']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Yöntem desteklenmiyor']);
        break;
}
