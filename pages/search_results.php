<?php
// Include dependencies
require __DIR__ . '/../includes/db_connect.php';
require __DIR__ . '/../includes/header.php';

// Initialize filter variables
$listing_type = $_GET['listing_type'] ?? '';
$tipe_properti = $_GET['tipe_properti'] ?? '';
$province = $_GET['province'] ?? '';
$regency = $_GET['regency'] ?? '';
$district_or_area = $_GET['district_or_area'] ?? '';
$harga_min = $_GET['harga_min'] ?? '';
$harga_max = $_GET['harga_max'] ?? '';

// Build dynamic SQL query
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.description, 
        p.price, 
        p.property_type,
        p.tipe_properti,
        p.id_properti,
        p.province,
        p.regency,
        p.district_or_area,
        p.luas_tanah,
        p.luas_bangunan,
        p.kamar_tidur,
        p.kamar_mandi,
        pi.image_path AS main_image_path
    FROM 
        properties p
    LEFT JOIN 
        (SELECT property_id, MIN(id) AS min_img_id FROM property_images GROUP BY property_id) AS min_images
    ON 
        p.id = min_images.property_id
    LEFT JOIN 
        property_images pi 
    ON 
        min_images.min_img_id = pi.id
    WHERE 1=1 
";

$params = [];

// Apply filters
if (!empty($listing_type)) {
    $sql .= " AND p.property_type = ?";
    $params[] = $listing_type;
}
if (!empty($tipe_properti)) {
    $sql .= " AND p.tipe_properti = ?";
    $params[] = $tipe_properti;
}
if (!empty($province)) {
    $sql .= " AND p.province = ?";
    $params[] = $province;
}
if (!empty($regency)) {
    $sql .= " AND p.regency = ?";
    $params[] = $regency;
}
if (!empty($district_or_area)) {
    $sql .= " AND p.district_or_area LIKE ?";
    $params[] = '%' . $district_or_area . '%';
}
if (!empty($harga_min) && is_numeric($harga_min)) {
    $sql .= " AND p.price >= ?";
    $params[] = $harga_min;
}
if (!empty($harga_max) && is_numeric($harga_max)) {
    $sql .= " AND p.price <= ?";
    $params[] = $harga_max;
}

$sql .= " ORDER BY p.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching properties: " . $e->getMessage());
    echo "<div class='container mx-auto px-4 py-6'><p class='text-red-600'>Terjadi kesalahan saat mengambil data properti.</p></div>";
    require __DIR__ . '/../includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Properti - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-lato bg-gray-100 text-gray-800">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl md:text-3xl font-bold text-blue-800 text-center mb-8">Hasil Pencarian Properti</h1>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if (empty($results)): ?>
                <p class="col-span-full text-center text-gray-600 py-10">Tidak ada properti yang ditemukan dengan kriteria tersebut. Coba kriteria lain.</p>
            <?php else: ?>
                <?php foreach ($results as $property): ?>
                    <a href="pages/detail_property.php?id=<?= htmlspecialchars($property['id']) ?>" class="block">
                        <div class="bg-white border-2 border-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-1 hover:shadow-xl transition">
                            <div class="relative w-full h-48">
                                <img src="../Uploads/properties/<?= htmlspecialchars($property['main_image_path'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                <div class="absolute bottom-2 left-2 bg-white text-black px-3 py-1 rounded text-sm font-bold border border-gray-200 shadow">
                                    Rp <?= number_format($property['price'], 0, ',', '.') ?>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 truncate"><?= htmlspecialchars($property['title']) ?></h3>
                                <span class="inline-block bg-blue-800 text-white text-xs px-2 py-1 rounded mb-2">
                                    <?= htmlspecialchars($property['tipe_properti'] ?? 'N/A') ?> (<?= htmlspecialchars($property['property_type'] == 'for_sale' ? 'Dijual' : 'Disewakan') ?>)
                                </span>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    <?= htmlspecialchars($property['district_or_area'] ? $property['district_or_area'] . ', ' : '') . htmlspecialchars($property['regency'] . ', ' . $property['province']) ?>
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                        <i class="fa-solid fa-expand text-blue-600"></i>
                                        <span><?= htmlspecialchars($property['luas_tanah'] ?? 'N/A') ?> m²</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                        <i class="fa-solid fa-house text-blue-600"></i>
                                        <span><?= htmlspecialchars($property['luas_bangunan'] ?? 'N/A') ?> m²</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                        <i class="fa-solid fa-bed text-blue-600"></i>
                                        <span><?= htmlspecialchars($property['kamar_tidur'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                        <i class="fa-solid fa-bath text-blue-600"></i>
                                        <span><?= htmlspecialchars($property['kamar_mandi'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- WhatsApp Chat Button -->
        <a href="https://wa.me/62123456789" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-full flex items-center gap-2 shadow-lg hover:bg-green-600 hover:-translate-y-1 transition">
            <i class="fab fa-whatsapp text-xl"></i> Butuh bantuan? Chat dengan kami
        </a>

        <!-- Back Link -->
        <a href="../pages/index.php" class="block text-blue-800 font-semibold hover:text-blue-900 hover:underline mt-8">← Kembali ke Halaman Utama</a>
    </div>

    <!-- Footer -->
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>