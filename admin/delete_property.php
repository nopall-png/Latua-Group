<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Cek dulu ada atau tidak
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    $property = $stmt->fetch();

    if ($property) {
        // Hapus properti (gambar di property_images ikut terhapus karena ON DELETE CASCADE)
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: properties.php");
exit;
