<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prevent cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Include DB & header
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/header.php';

// Query properties
$properties = [];
try {
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
} catch (PDOException $e) {
    error_log("Error fetching properties: " . $e->getMessage());
    $properties = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Latuae Land</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-6xl mx-auto py-10 px-5">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Admin Panel - Daftar Properti</h1>

    <a href="/LatuaGroup/admin/upload_property.php" 
       class="mb-4 inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
       Tambah Properti Baru
    </a>

    <?php if (empty($properties)): ?>
      <p class="text-gray-600">Belum ada properti yang terdaftar.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($properties as $property): ?>
          <div class="border border-gray-300 rounded-lg shadow-md overflow-hidden bg-white hover:scale-105 transition">
            <a href="/LatuaGroup/pages/detail_property.php?id=<?= $property['id']; ?>" class="block">
              <?php
              $image_path = !empty($property['main_image_path']) 
                  ? "/LatuaGroup/Uploads/properties/" . htmlspecialchars($property['main_image_path']) 
                  : "/LatuaGroup/Uploads/default.jpg";
              ?>
              <img src="<?= $image_path ?>?t=<?= time(); ?>" 
                   alt="<?= htmlspecialchars($property['title']); ?>" 
                   class="w-full h-48 object-cover">
            </a>
            <div class="p-4">
              <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($property['title']); ?></h3>
              <p class="text-gray-600"><?= htmlspecialchars($property['province'] . ', ' . $property['regency']); ?></p>
              <p class="text-gray-600 mt-1">Rp <?= number_format($property['price'], 0, ',', '.'); ?></p>
              <p class="text-sm text-gray-500">
                <?= $property['property_type'] === 'for_sale' ? 'Dijual' : 'Disewa'; ?>
              </p>
              <div class="mt-4 flex justify-between">
                <a href="/LatuaGroup/admin/edit_property.php?id=<?= $property['id']; ?>" 
                   class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">Edit</a>
                <a href="/LatuaGroup/admin/delete_property.php?id=<?= $property['id']; ?>" 
                   class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                   onclick="return confirm('Yakin ingin menghapus properti ini?');">
                   Hapus
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/footer.php'; ?>
</body>
</html>
