<?php
$host = 'gateway01.us-west-2.prod.aws.tidbcloud.com';
$port = 4000;
$db   = 'test';
$user = '3M6Gd9ZorXxRiej.root';
$pass = '1byKIdLMUMpkVCa5';
$charset = 'utf8mb4';

// Make sure this file exists after Docker copy
$caCertPath = __DIR__ . '/isrgrootx1.pem';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::MYSQL_ATTR_SSL_CA => $caCertPath,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Connected successfully to TiDB Cloud!";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>
<?php
echo file_exists(__DIR__ . '/isrgrootx1.pem') ? "✅ Cert found" : "❌ Cert missing";
?>
