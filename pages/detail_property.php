<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

// Ambil ID property dari URL
$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    die("Property tidak ditemukan.");
}

// Ambil data property + agen
$stmt = $pdo->prepare("
    SELECT p.*, a.name AS agent_name, a.phone_number, a.email, a.photo_path,
           GROUP_CONCAT(pi.image_path) AS images
    FROM properties p
    LEFT JOIN agents a ON p.agent_id = a.id
    LEFT JOIN property_images pi ON p.id = pi.property_id
    WHERE p.id = ?
    GROUP BY p.id
");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Property tidak ditemukan.");
}

$images = $property['images'] ? explode(',', $property['images']) : ['default.jpg'];
?>

<?php include '../includes/header.php'; ?>

<!-- Back Button -->
<div class="max-w-6xl mx-auto py-4">
  <a href="index.php" class="text-sm text-gray-500 hover:underline">← Balik ke pencarian</a>
</div>

<!-- Property Content -->
<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
  
  <!-- Gambar + Detail -->
  <div class="lg:col-span-2 space-y-4">
    <!-- Gambar utama + thumbnail -->
    <div class="grid grid-cols-3 gap-2">
      <img src="/LatuaGroup/uploads/properties/<?= $images[0] ?>" 
           class="col-span-2 w-full h-80 object-cover rounded-lg">
      <div class="space-y-2">
        <?php foreach (array_slice($images, 1, 3) as $img): ?>
          <img src="/LatuaGroup/uploads/properties/<?= $img ?>" 
               class="w-full h-24 object-cover rounded-lg">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Info Properti -->
    <h2 class="text-2xl font-bold">
      Rp <?= number_format($property['price'], 0, ',', '.') ?> 
      <?= $property['property_type'] === 'for_sale' ? "Jual" : "Sewa" ?>
    </h2>
    <h1 class="text-xl text-blue-700 font-semibold"><?= htmlspecialchars($property['title']) ?></h1>
    <p class="text-gray-600"><?= htmlspecialchars($property['regency']) ?>, <?= htmlspecialchars($property['province']) ?></p>
    <p class="text-sm text-gray-400">Posted On: <?= date("j F Y", strtotime($property['created_at'])) ?></p>

    <!-- Deskripsi -->
    <div class="mt-6">
      <h3 class="text-lg font-bold mb-2">Deskripsi</h3>
      <p class="text-gray-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($property['description'])) ?></p>
    </div>

    <!-- Fasilitas -->
    <div class="mt-6">
      <h3 class="text-lg font-bold mb-2">Facilities</h3>
      <ul class="list-disc pl-5 text-gray-700 space-y-1">
        <li>24 jam keamanan</li>
        <li>Wi-Fi Free</li>
      </ul>
    </div>
  </div>

  <!-- Agent Card -->
  <aside class="bg-white shadow rounded-lg p-6 text-center border">
    <img src="/LatuaGroup/uploads/agents/<?= htmlspecialchars($property['photo_path'] ?? 'default.jpg') ?>" 
         class="w-24 h-24 mx-auto rounded-full object-cover">
    <h3 class="mt-3 font-bold"><?= htmlspecialchars($property['agent_name'] ?? 'Tidak ada agen') ?></h3>
    <p class="text-gray-500 text-sm"><?= htmlspecialchars($property['phone_number'] ?? '-') ?></p>
    <p class="text-gray-500 text-sm"><?= htmlspecialchars($property['email'] ?? '-') ?></p>
    <div class="mt-4 space-x-2">
      <a href="tel:<?= $property['phone_number'] ?>" class="px-3 py-1 bg-green-600 text-white rounded">📞 Telpon</a>
      <a href="mailto:<?= $property['email'] ?>" class="px-3 py-1 bg-blue-600 text-white rounded">✉ Email</a>
    </div>
  </aside>
</div>

<?php include '../includes/footer.php'; ?>
