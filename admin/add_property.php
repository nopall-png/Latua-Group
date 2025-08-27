<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $province = $_POST['province'];
    $regency = $_POST['regency'];
    $description = $_POST['description'];

    // Upload gambar
    $fileName = null;
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/LatuaGroup/Uploads/properties/" . $fileName);
    }

    $stmt = $pdo->prepare("INSERT INTO properties (title, price, province, regency, description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $price, $province, $regency, $description, $fileName]);

    header("Location: properties.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Tambah Properti</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Tambah Properti</h1>
    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded shadow">
      <input type="text" name="title" placeholder="Judul Properti" class="w-full border rounded p-2" required>
      <input type="number" name="price" placeholder="Harga" class="w-full border rounded p-2" required>
      <input type="text" name="province" placeholder="Provinsi" class="w-full border rounded p-2" required>
      <input type="text" name="regency" placeholder="Kabupaten/Kota" class="w-full border rounded p-2" required>
      <textarea name="description" placeholder="Deskripsi" class="w-full border rounded p-2" rows="4"></textarea>
      <input type="file" name="image" class="w-full">
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
    </form>
  </div>
</body>
</html>
