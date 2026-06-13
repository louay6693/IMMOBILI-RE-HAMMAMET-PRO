
<?php
require_once __DIR__ . '/../connection.php';

class Maison {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

   public function getAll() {
    $stmt = $this->pdo->query("
        SELECT 
            m.*,
            (SELECT url FROM photos_maison WHERE maison_id = m.id ORDER BY ordre ASC LIMIT 1) AS photo_url
        FROM maisons m
        ORDER BY m.created_at DESC
    ");
    return $stmt->fetchAll();
}

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM maisons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO maisons (nom, prix, latitude, longitude, nb_chambres, nb_toilettes, cuisine, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nom'], $data['prix'], $data['latitude'], $data['longitude'],
            $data['nb_chambres'], $data['nb_toilettes'], $data['cuisine'], $data['description']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM maisons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>