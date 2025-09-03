<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $province = $_POST['province'] ?? '';
    $regency = $_POST['regency'] ?? '';
    $property_type = $_POST['property_type'] ?? 'for_sale';

    try {
        // Insert ke tabel properties
        $stmt = $pdo->prepare("
            INSERT INTO properties (title, description, price, province, regency, property_type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $price, $province, $regency, $property_type]);
        $propertyId = $pdo->lastInsertId();

        // Upload gambar
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/LatuaGroup/uploads/properties/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $newFileName = uniqid("prop_") . "." . $ext;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        // Simpan nama file ke property_images
                        $stmtImg = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                        $stmtImg->execute([$propertyId, $newFileName]);
                    }
                }
            }
        }

        header("Location: /LatuaGroup/admin/index.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
