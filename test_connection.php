<?php
$host = 'switchyard.proxy.rlwy.net';
$port = 17980;
$dbname = 'railway';
$username = 'root';
$password = 'EAcCSqpibCJNebvybnvjhnsBcPPgZzoi';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to MySQL database!\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
