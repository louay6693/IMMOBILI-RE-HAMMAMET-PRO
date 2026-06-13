<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../connection.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dtfuvv3g9',
        'api_key'    => '684319687233571',
        'api_secret' => 'TLsKhJtoOCwtBppwRIla1lQ4-JE',
    ],
    'url' => ['secure' => true]
]);

try {
    $id           = intval($_POST['id']);
    $nom          = $_POST['nom'];
    $prix         = $_POST['prix'];
    $nb_chambres  = $_POST['nb_chambres'];
    $nb_toilettes = $_POST['nb_toilettes'];
    $cuisine      = $_POST['cuisine'] === 'true' ? 'true' : 'false';
    $disponible   = $_POST['disponible'] === 'true' ? 'true' : 'false';
    $latitude     = !empty($_POST['latitude'])  ? $_POST['latitude']  : null;
    $longitude    = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
    $description  = $_POST['description'] ?? '';

    // Mettre à jour la maison
    $stmt = $pdo->prepare("
        UPDATE maisons SET
            nom = ?, prix = ?, nb_chambres = ?, nb_toilettes = ?,
            cuisine = ?, disponible = ?, latitude = ?, longitude = ?, description = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $nom, $prix, $nb_chambres, $nb_toilettes,
        $cuisine, $disponible, $latitude, $longitude, $description, $id
    ]);

    // Upload nouvelles photos si présentes
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadApi = new UploadApi();
        $count = count($_FILES['photos']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $result = $uploadApi->upload($_FILES['photos']['tmp_name'][$i], [
                'folder'    => 'hammamet_maisons',
                'public_id' => 'maison_' . $id . '_edit_' . time() . '_' . $i,
            ]);
            $stmt2 = $pdo->prepare("INSERT INTO photos_maison (maison_id, url, ordre) VALUES (?, ?, ?)");
            $stmt2->execute([$id, $result['secure_url'], $i]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Maison modifiée avec succès']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>