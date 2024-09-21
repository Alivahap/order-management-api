<?php
// /public/index.php

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// /public/index.php

$requestUri = $_SERVER['REQUEST_URI'];

// URL'den /public/index.php kısmını temizle
$requestUri = preg_replace('/^\/public\/index.php/', '', $requestUri);

$requestMethod = $_SERVER['REQUEST_METHOD'];

if (preg_match('/^\/api\/orders/', $requestUri)) {
    include __DIR__ . '/../api/order.php';
} elseif (preg_match('/^\/api\/products/', $requestUri)) {
    include __DIR__ . '/../api/product.php';
} elseif (preg_match('/^\/api\/customers/', $requestUri)) {
    include __DIR__ . '/../api/customer.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint bulunamadı']);
}

