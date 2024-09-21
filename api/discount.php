<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Discount.php';

//require_once '../config/database.php';
//require_once '../models/Order.php';

// Tüm siparişleri al
$orders = getAllOrders();

if (empty($orders)) {
    http_response_code(404);
    echo json_encode(['error' => 'Hiç sipariş bulunamadı']);
    exit;
}

// İndirim hesaplama
$discountModel = new Discount();
$results = [];

foreach ($orders as $order) {
    $discountResults = $discountModel->calculateDiscounts($order);
    $results[] = [
        'orderId' => $order['id'],
        'discounts' => $discountResults['discounts'],
        'totalDiscount' => $discountResults['totalDiscount'],
        'discountedTotal' => $discountResults['discountedTotal']
    ];
}

echo json_encode($results);

// Tüm siparişleri veritabanından çekmek için bir fonksiyon
function getAllOrders() {
    global $pdo; // Veritabanı bağlantısını kullan
    $stmt = $pdo->prepare("SELECT * FROM orders");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as &$order) {
        // Sipariş kalemlerini de çekelim
        $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmtItems->execute(['order_id' => $order['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        $order['items'] = $items;
    }

    return $orders;
}
