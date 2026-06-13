<?php
require_once __DIR__ . '/../connection.php';
header('Content-Type: application/json');

$maison_id = intval($_GET['maison_id'] ?? 0);
if (!$maison_id) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, url, ordre FROM photos_maison WHERE maison_id = ? ORDER BY ordre ASC");
$stmt->execute([$maison_id]);
$photos = $stmt->fetchAll();

echo json_encode(['success' => true, 'photos' => $photos]);
?>