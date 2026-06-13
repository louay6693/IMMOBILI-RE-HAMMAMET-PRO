<?php
$host     = getenv('DB_HOST');
$dbname   = getenv('DB_NAME');
$user     = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$port     = getenv('DB_PORT');
$endpoint = getenv('DB_ENDPOINT');

echo "HOST: " . ($host ? "✅ found" : "❌ empty") . "<br>";
echo "NAME: " . ($dbname ? "✅ found" : "❌ empty") . "<br>";
echo "USER: " . ($user ? "✅ found" : "❌ empty") . "<br>";
echo "PASSWORD: " . ($password ? "✅ found" : "❌ empty") . "<br>";
echo "PORT: " . ($port ? "✅ found" : "❌ empty") . "<br>";
echo "ENDPOINT: " . ($endpoint ? "✅ found" : "❌ empty") . "<br>";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;options=endpoint=$endpoint;sslmode=require",
        $user,
        $password
    );
    echo "<br>✅ DATABASE CONNECTED!";
} catch (PDOException $e) {
    echo "<br>❌ ERROR: " . $e->getMessage();
}
?>