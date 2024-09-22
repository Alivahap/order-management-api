<?php
$host = 'db';
$dbname = 'app_db';
$username = 'user';
$password = 'user_password';

$retries = 5; // 5 kez yeniden deneme
$retryDelay = 5; // 5 saniye bekleme

for ($i = 0; $i < $retries; $i++) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
        break;
    } catch (PDOException $e) {
        if ($i == $retries - 1) {
            die("Veritabanına bağlanılamadı: 5 saniye sonra tekrar denenecek..." . $e->getMessage());
        }
        sleep($retryDelay);
    }
}
