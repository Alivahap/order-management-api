<?php
// /api/order.php

require_once '../config/database.php';
require_once '../models/Order.php';

$order = new Order($pdo);

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($order->getAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['customerId']) && isset($data['items'])) {
            $result = $order->create($data['customerId'], $data['items']);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Geçersiz veri']);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $result = $order->delete($id);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Sipariş ID belirtilmedi']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Yöntem desteklenmiyor']);
        break;
}

?>