<?php
require_once __DIR__ . '/../model/Maison.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$maison = new Maison();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {

    // GET toutes les maisons ou une seule
    case 'GET':
        if ($action === 'getOne' && isset($_GET['id'])) {
            $data = $maison->getById($_GET['id']);
            if ($data) {
                echo json_encode(["success" => true, "data" => $data]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "error" => "Maison non trouvée"]);
            }
        } else {
            $data = $maison->getAll();
            echo json_encode(["success" => true, "data" => $data]);
        }
        break;

    // POST créer une maison
    case 'POST':
        if ($action === 'create') {
            $body = json_decode(file_get_contents('php://input'), true);

            $required = ['nom', 'prix', 'nb_chambres', 'nb_toilettes'];
            foreach ($required as $field) {
                if (empty($body[$field])) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "error" => "Champ '$field' manquant"]);
                    exit;
                }
            }

            $result = $maison->create([
                'nom'          => $body['nom'],
                'prix'         => $body['prix'],
                'latitude'     => $body['latitude'] ?? null,
                'longitude'    => $body['longitude'] ?? null,
                'nb_chambres'  => $body['nb_chambres'],
                'nb_toilettes' => $body['nb_toilettes'],
                'cuisine'      => $body['cuisine'] ?? true,
                'description'  => $body['description'] ?? ''
            ]);

            if ($result) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Maison créée avec succès"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "error" => "Erreur lors de la création"]);
            }
        }
        break;

    // DELETE supprimer une maison
    case 'DELETE':
        if ($action === 'delete' && isset($_GET['id'])) {
            $result = $maison->delete($_GET['id']);
            if ($result) {
                echo json_encode(["success" => true, "message" => "Maison supprimée"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "error" => "Erreur lors de la suppression"]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
        break;
}
?>