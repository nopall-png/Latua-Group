<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: properties.php");
    exit;
}

// Ambil data lama
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "Properti tidak ditemukan.";
    exit;
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $province = $_POST['province'];
    $regency = $_POST['regency'];
    $description = $_POST['description'];

    $fileName = $property['image'];
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/LatuaGroup/Uploads/properties/" . $fileName);
    }

    $stmt = $pdo->prepare("UPDATE properties SET title=?, price=?, province=?, regency=?, description=?, image=? WHERE id=?");
    $stmt->execute([$title, $price, $province, $regency, $description, $fileName, $id]);

    header("Location: properties.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Properti</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Edit Properti</h1>
    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded shadow">
      <input type="text" name="title" value="<?= htmlspecialchars($property['title']) ?>" class="w-full border rounded p-2" required>
      <input type="number" name="price" value="<?= $property['price'] ?>" class="w-full border rounded p-2" required>
      <input type="text" name="province" value="<?= htmlspecialchars($property['province']) ?>" class="w-full border rounded p-2" required>
      <input type="text" name="regency" value="<?= htmlspecialchars($property['regency']) ?>" class="w-full border rounded p-2" required>
      <textarea name="description" class="w-full border rounded p-2" rows="4"><?= htmlspecialchars($property['description']) ?></textarea>
      <input type="file" name="image" class="w-full">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
    </form>
  </div>
</body>
</html>
