<?php
// Enable error reporting
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
        // Insert property ke tabel properties
        $stmt = $pdo->prepare("
            INSERT INTO properties (title, description, price, province, regency, property_type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $price, $province, $regency, $property_type]);
        $propertyId = $pdo->lastInsertId();

        // === Upload Images ===
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/LatuaGroup/uploads/properties/";

            // Pastikan folder ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $newFileName = uniqid("prop_") . "." . $ext;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        // Simpan ke DB hanya nama file
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

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Properti</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-2xl mx-auto py-10 px-5">
    <h1 class="text-2xl font-semibold mb-6">Tambah Properti Baru</h1>
    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded-lg shadow">
      <div>
        <label class="block mb-1">Judul</label>
        <input type="text" name="title" class="w-full border px-3 py-2 rounded" required>
      </div>
      <div>
        <label class="block mb-1">Deskripsi</label>
        <textarea name="description" class="w-full border px-3 py-2 rounded" required></textarea>
      </div>
      <div>
        <label class="block mb-1">Harga</label>
        <input type="number" name="price" class="w-full border px-3 py-2 rounded" required>
      </div>
      <div>
        <label class="block mb-1">Provinsi</label>
        <input type="text" name="province" class="w-full border px-3 py-2 rounded">
      </div>
      <div>
        <label class="block mb-1">Kabupaten/Kota</label>
        <input type="text" name="regency" class="w-full border px-3 py-2 rounded">
      </div>
      <div>
        <label class="block mb-1">Tipe Properti</label>
        <select name="property_type" class="w-full border px-3 py-2 rounded">
          <option value="for_sale">Dijual</option>
          <option value="for_rent">Disewa</option>
        </select>
      </div>
      <div>
        <label class="block mb-1">Foto Properti (boleh lebih dari 1)</label>
        <input type="file" name="images[]" accept="image/*" multiple>
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Simpan
      </button>
    </form>
  </div>
</body>
</html>
