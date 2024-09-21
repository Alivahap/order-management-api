<?php
// /models/Order.php

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tüm siparişleri getirir
    public function getAll() {
        // Siparişleri getir
        $stmt = $this->pdo->query("SELECT * FROM orders");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Her sipariş için ilgili order_items'i ekle
        foreach ($orders as &$order) {
            // Siparişe ait order_items'i getir
            $orderId = $order['id'];
            $stmt = $this->pdo->prepare("
                SELECT product_id as productId, quantity, unit_price as unitPrice, total 
                FROM order_items 
                WHERE order_id = :order_id
            ");
            $stmt->execute(['order_id' => $orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Siparişe ait öğeleri ekle
            $order['items'] = $items;

            // customerId ile müşteri bilgilerini de güncelleyebiliriz
            $order['customer_id'] = $order['customer_id']; // İsteğe bağlı düzenleme
        }

        return $orders;
    }

    // Sipariş oluşturma işlemi
   public function create($customerId, $items) {
    // Payload validasyonu
    if (!is_numeric($customerId) || $customerId <= 0) {
        return ['error' => 'Geçersiz müşteri ID'];
    }

    if (!is_array($items) || empty($items)) {
        return ['error' => 'Geçersiz ürün listesi'];
    }

    foreach ($items as $item) {
        if (!isset($item['productId']) || !is_numeric($item['productId']) || $item['productId'] <= 0) {
            return ['error' => 'Geçersiz ürün ID'];
        }
        if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
            return ['error' => 'Geçersiz miktar'];
        }
    }

    $this->pdo->beginTransaction();
    $total = 0; // $total başlangıç değeri verildi

    try {
        // İlk olarak siparişi orders tablosuna ekliyoruz (toplam 0 olarak)
        $stmt = $this->pdo->prepare("INSERT INTO orders (customer_id, total) VALUES (?, ?)");
        $stmt->execute([$customerId, $total]);

        // Eklenen siparişin ID'sini alıyoruz
        $orderId = $this->pdo->lastInsertId();

        // Ürünler üzerinden geçiyoruz
        foreach ($items as $item) {
            // Ürün bilgilerini veritabanından çekiyoruz
            $productStmt = $this->pdo->prepare("SELECT name, price, stock, category FROM products WHERE id = ?");
            $productStmt->execute([$item['productId']]);
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);

            // Stok kontrolü yapıyoruz
            if (!$product || $product['stock'] < $item['quantity']) {
                $this->pdo->rollBack();
                return ['error' => 'Stok yetersiz: ' . ($product ? $product['name'] : 'Bilinmeyen ürün')];
            }

            // Ürünün toplam fiyatını hesaplıyoruz ve $total'a ekliyoruz
            $itemTotal = $item['quantity'] * $product['price'];
            $total += $itemTotal;

            // order_items tablosuna ekliyoruz, category_id'yi de ekliyoruz
            $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, total, category_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['productId'], $item['quantity'], $product['price'], $itemTotal, $product['category']]);

            // Stok güncellemesi yapıyoruz
            $productStmt = $this->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $productStmt->execute([$item['quantity'], $item['productId']]);
        }

        // Siparişin toplam tutarını güncelliyoruz
        $stmt = $this->pdo->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->execute([$total, $orderId]);

        // Tüm işlemleri başarıyla tamamladığımızda commit yapıyoruz
        $this->pdo->commit();
        return ['orderId' => $orderId, 'total' => $total];
    } catch (Exception $e) {
        // Hata durumunda işlemleri geri alıyoruz
        $this->pdo->rollBack();
        return ['error' => $e->getMessage()];
    }
}

    // Sipariş silme işlemi
  public function delete($id) {
    $this->pdo->beginTransaction();

    try {
        // İlk olarak ilgili order_items kayıtlarını siliyoruz
        $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);

        // Daha sonra orders tablosundaki siparişi siliyoruz
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);

        // Tüm işlemleri başarılı bir şekilde tamamladığımızda commit yapıyoruz
        $this->pdo->commit();
        return ['message' => 'Sipariş silindi'];
    } catch (Exception $e) {
        // Hata durumunda işlemleri geri alıyoruz
        $this->pdo->rollBack();
        return ['error' => $e->getMessage()];
    }
}

	
	
	
}
?>
