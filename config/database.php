<?php
/*
$dsn = 'mysql:host=db;dbname=app_db';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
   
}
*/
?>
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
        //echo "Veritabanına başarıyla bağlanıldı!";
        break;
    } catch (PDOException $e) {
        if ($i == $retries - 1) {
            die("Veritabanına bağlanılamadı: " . $e->getMessage());
        }
        echo "Veritabanına bağlanılamadı. $retryDelay saniye sonra tekrar denenecek...\n";
        sleep($retryDelay);
    }
}
