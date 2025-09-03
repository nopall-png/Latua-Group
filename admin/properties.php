<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

$stmt = $pdo->query("
    SELECT p.id, p.title, p.price, p.province, p.regency, p.property_type,
           pi.image_path AS main_image_path
    FROM properties p
    LEFT JOIN (
        SELECT property_id, MIN(id) AS min_img_id 
        FROM property_images GROUP BY property_id
    ) AS min_images ON p.id = min_images.property_id
    LEFT JOIN property_images pi ON min_images.min_img_id = pi.id
    ORDER BY p.created_at DESC
");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Properti - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="min-h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0E1B4D] text-white p-6 space-y-6">
      <h1 class="text-2xl font-bold">Latuae Admin</h1>
      <nav class="space-y-4">
        <a href="index.php" class="block hover:text-gray-300">🏠 Dashboard</a>
        <a href="properties.php" class="block hover:text-gray-300">🏡 Properti</a>
        <a href="agents.php" class="block hover:text-gray-300">👨‍💼 Agen</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-4">Daftar Properti</h1>
      <a href="add_property.php" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">+ Tambah Properti</a>
      
      <table class="w-full bg-white shadow rounded">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-3 text-left">Gambar</th>
            <th class="p-3 text-left">Judul</th>
            <th class="p-3 text-left">Harga</th>
            <th class="p-3 text-left">Lokasi</th>
            <th class="p-3 text-left">Tipe</th>
            <th class="p-3 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($properties as $p): ?>
            <tr class="border-t">
              <td class="p-3">
                <img src="/LatuaGroup/uploads/properties/<?= $p['main_image_path'] ?: 'default.jpg' ?>" 
                     alt="<?= htmlspecialchars($p['title']) ?>" 
                     class="w-20 h-16 object-cover rounded">
              </td>
              <td class="p-3"><?= htmlspecialchars($p['title']) ?></td>
              <td class="p-3">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
              <td class="p-3"><?= htmlspecialchars($p['regency']) ?>, <?= htmlspecialchars($p['province']) ?></td>
              <td class="p-3"><?= $p['property_type'] === 'for_sale' ? 'Dijual' : 'Disewa' ?></td>
              <td class="p-3">
                <a href="edit_property.php?id=<?= $p['id'] ?>" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>
                <a href="delete_property.php?id=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-3 py-1 bg-red-600 text-white rounded">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
