<?php
$host     = getenv('DB_HOST');
$dbname   = getenv('DB_NAME');
$user     = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$port     = getenv('DB_PORT');
$endpoint = getenv('DB_ENDPOINT');

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;options=endpoint=$endpoint;sslmode=require",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die(json_encode(["error" => "Connexion échouée: " . $e->getMessage()]));
}
?>