<?php
require_once '../connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['photos']) || !is_array($data['photos'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    // $data['photos'] = [ ['id' => 3, 'ordre' => 0], ['id' => 1, 'ordre' => 1], ... ]
    foreach ($data['photos'] as $photo) {
        $stmt = $pdo->prepare(
            "UPDATE photos_maison SET ordre = :ordre WHERE id = :id"
        );
        $stmt->execute([
            ':ordre' => (int)$photo['ordre'],
            ':id'    => (int)$photo['id']
        ]);
    }
    echo json_encode(['success' => true, 'message' => 'Ordre mis à jour']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}