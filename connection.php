<?php
$host     = getenv('DB_HOST')     ?: "ep-patient-glade-asfalp0w.c-4.eu-central-1.aws.neon.tech";
$dbname   = getenv('DB_NAME')     ?: "neondb";
$user     = getenv('DB_USER')     ?: "neondb_owner";
$password = getenv('DB_PASSWORD') ?: "npg_IAa7hgJKbT5x";
$port     = getenv('DB_PORT')     ?: "5432";
$endpoint = getenv('DB_ENDPOINT') ?: "ep-patient-glade-asfalp0w";

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