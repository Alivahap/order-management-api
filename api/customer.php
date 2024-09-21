<?php
// /api/customer.php

require_once '../config/database.php';
require_once '../models/Customer.php';

$customer = new Customer($pdo);

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($customer->getAll());
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Yöntem desteklenmiyor']);
        break;
}
