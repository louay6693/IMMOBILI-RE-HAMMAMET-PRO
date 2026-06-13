<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../connection.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dtfuvv3g9',
        'api_key'    => '684319687233571',
        'api_secret' => 'TLsKhJtoOCwtBppwRIla1lQ4-JE',
    ],
    'url' => ['secure' => true]
]);

try {
    // 1. Créer la maison dans Neon DB
    $nom          = $_POST['nom'] ?? '';
    $prix         = $_POST['prix'] ?? 0;
    $nb_chambres  = $_POST['nb_chambres'] ?? 1;
    $nb_toilettes = $_POST['nb_toilettes'] ?? 1;
    $cuisine_val = $_POST['cuisine'] ?? 'true';
    $cuisine = ($cuisine_val === 'true' || $cuisine_val === '1' || $cuisine_val === true) ? 'true' : 'false';
    $latitude  = !empty($_POST['latitude'])  ? $_POST['latitude']  : null;
$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
    $description  = $_POST['description'] ?? '';

    if (!$nom || !$prix || !$nb_chambres || !$nb_toilettes) {
        echo json_encode(['success' => false, 'error' => 'Champs obligatoires manquants']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO maisons (nom, prix, latitude, longitude, nb_chambres, nb_toilettes, cuisine, description, disponible)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, true)
        RETURNING id
    ");
    $stmt->execute([$nom, $prix, $latitude ?: null, $longitude ?: null, $nb_chambres, $nb_toilettes, $cuisine, $description]);
    $maison_id = $stmt->fetchColumn();

    // 2. Upload photos vers Cloudinary + sauvegarder URLs
    $uploaded_urls = [];

    if (!empty($_FILES['photos']['name'][0])) {
        $uploadApi = new UploadApi();
        $count = count($_FILES['photos']['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $result = $uploadApi->upload($_FILES['photos']['tmp_name'][$i], [
                'folder'    => 'hammamet_maisons',
                'public_id' => 'maison_' . $maison_id . '_' . $i,
            ]);

            $url = $result['secure_url'];
            $uploaded_urls[] = $url;

            $stmt2 = $pdo->prepare("
                INSERT INTO photos_maison (maison_id, url, ordre)
                VALUES (?, ?, ?)
            ");
            $stmt2->execute([$maison_id, $url, $i]);
        }
    }

    echo json_encode([
        'success'   => true,
        'maison_id' => $maison_id,
        'photos'    => $uploaded_urls,
        'message'   => 'Maison ajoutée avec succès'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>