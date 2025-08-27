<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';
$properties = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin - Properti</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-6xl mx-auto py-10 px-6">
    <h1 class="text-2xl font-bold mb-6">Daftar Properti</h1>
    <a href="add_property.php" class="mb-6 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">+ Tambah Properti</a>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($properties as $p): ?>
        <div class="bg-white border rounded-lg shadow">
          <img src="/LatuaGroup/Uploads/properties/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" class="w-full h-40 object-cover rounded-t-lg">
          <div class="p-4">
            <h3 class="text-lg font-semibold"><?= htmlspecialchars($p['title']) ?></h3>
            <p class="text-gray-600">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($p['province'] . ', ' . $p['regency']) ?></p>
            <div class="mt-3 flex justify-between">
              <a href="edit_property.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
              <a href="delete_property.php?id=<?= $p['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus properti ini?')">Hapus</a>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</body>
</html>
