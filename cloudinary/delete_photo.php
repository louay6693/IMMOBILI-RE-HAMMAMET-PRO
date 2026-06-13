<?php
require_once __DIR__ . '/../connection.php';
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
$id = intval($body['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM photos_maison WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['success' => true]);
?>