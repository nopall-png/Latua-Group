<?php
include 'includes/db_connect.php';
include 'includes/header.php';

// Inisialisasi variabel filter
$listing_type = $_GET['listing_type'] ?? '';
$tipe_properti = $_GET['tipe_properti'] ?? ''; // Ini adalah tipe properti spesifik (Rumah, Tanah, dll.)
$province = $_GET['province'] ?? '';
$regency = $_GET['regency'] ?? '';
$district_or_area = $_GET['district_or_area'] ?? '';
$harga_min = $_GET['harga_min'] ?? '';
$harga_max = $_GET['harga_max'] ?? '';

// Membangun query SQL dinamis
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.description, 
        p.price, 
        p.property_type,
        p.tipe_properti, -- Pastikan kolom ini ada di tabel properties
        p.id_properti,
        p.province,
        p.regency,
        p.district_or_area,
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

// Filter berdasarkan tipe listing (dijual/disewakan)
if (!empty($listing_type)) {
    $sql .= " AND p.property_type = ?";
    $params[] = $listing_type;
}
// Filter berdasarkan tipe properti spesifik (Rumah, Tanah, dll.)
if (!empty($tipe_properti)) {
    $sql .= " AND p.tipe_properti = ?"; // Pastikan kolom di DB bernama 'tipe_properti'
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
    $params[] = '%' . $district_or_area . '%'; // Pencarian sebagian
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

?>

<div class="container">
    <h1>Hasil Pencarian Properti</h1>

    <?php if (empty($results)): ?>
        <p>Tidak ada properti yang ditemukan dengan kriteria tersebut. Coba kriteria lain.</p>
    <?php else: ?>
        <div class="property-grid">
            <?php foreach ($results as $property): ?>
                <a href="detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                    <div class="property-card">
                        <img src="Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p>Lokasi: <?php echo htmlspecialchars($property['district_or_area'] ? $property['district_or_area'] . ', ' : '') . htmlspecialchars($property['regency'] . ', ' . $property['province']); ?></p>
                        <p>Harga: Rp<?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                        <p>Tipe: <?php 
                            // Tampilkan tipe properti spesifik dan jenis listing
                            echo htmlspecialchars($property['tipe_properti'] ?? 'N/A') . ' (' . htmlspecialchars($property['property_type'] == 'for_sale' ? 'Dijual' : 'Disewakan') . ')'; 
                        ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p style="margin-top: 30px;"><a href="index.php">Kembali ke Halaman Utama</a></p>
</div>

<?php include 'includes/footer.php'; ?>