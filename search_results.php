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

// Filter berdasarkan tipe listing (dijual/disewakan)
if (!empty($listing_type)) {
    $sql .= " AND p.property_type = ?";
    $params[] = $listing_type;
}
// Filter berdasarkan tipe properti spesifik (Rumah, Tanah, dll.)
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Properti</title>
    <style>
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Lato', sans-serif;
        }
        
        h1 {
            color: #334894;
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* Property Grid */
        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        /* Property Card */
        .property-card-link {
            text-decoration: none;
            color: inherit;
        }
        
        .property-card {
            background: #FFFFFF;
            border: 2px solid #334894;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(51, 72, 148, 0.2);
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(51, 72, 148, 0.3);
        }
        
        .property-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .property-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .property-card:hover .property-image-container img {
            transform: scale(1.03);
        }
        
        /* Price Overlay */
        .price-overlay {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: white;
            color: black;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }

        .price-text {
            font-size: 1rem;
            font-weight: bold;
            color: #000;
            white-space: nowrap;
        }
        
        /* Card Content */
        .property-card-content {
            padding: 15px;
        }
        
        .property-title {
            font-size: 1.2rem;
            color: #000000;
            margin-bottom: 10px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .property-location {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .property-type {
            display: inline-block;
            background-color: #334894;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .property-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #555;
            background: #f8f9fa;
            padding: 6px 10px;
            border-radius: 6px;
        }

        .detail-item i {
            color: #334894;
            font-size: 0.9rem;
            min-width: 16px;
            text-align: center;
        }

        .detail-value {
            font-weight: 500;
            color: #333;
        }
        
        /* No Properties Message */
        .no-properties {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px 0;
            color: #666;
        }
        
        /* Back Link */
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #334894;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #1a2c5e;
            text-decoration: underline;
        }
        
        /* WhatsApp Chat Button */
        .whatsapp-chat {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25D366;
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .whatsapp-chat i {
            font-size: 24px;
        }
        .whatsapp-chat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hasil Pencarian Properti</h1>
    
    <div class="property-grid">
        <?php if (empty($results)): ?>
            <p class="no-properties">Tidak ada properti yang ditemukan dengan kriteria tersebut. Coba kriteria lain.</p>
        <?php else: ?>
            <?php foreach ($results as $property): ?>
                <a href="detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                    <div class="property-card">
                        <div class="property-image-container">
                            <img src="Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                            <div class="price-overlay">
                                <p class="price-text">Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="property-card-content">
                            <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                            <span class="property-type">
                                <?php 
                                    echo htmlspecialchars($property['tipe_properti'] ?? 'N/A') . ' (' . 
                                    htmlspecialchars($property['property_type'] == 'for_sale' ? 'Dijual' : 'Disewakan') . ')'; 
                                ?>
                            </span>
                            <p class="property-location">
                                <?php 
                                    echo htmlspecialchars($property['district_or_area'] ? $property['district_or_area'] . ', ' : '') . 
                                    htmlspecialchars($property['regency'] . ', ' . $property['province']);
                                ?>
                            </p>
                            <div class="property-details">
                                <div class="detail-item">
                                    <i class="fa-solid fa-expand"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fa-solid fa-house"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fa-solid fa-bed"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fa-solid fa-bath"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="https://wa.me/62123456789" class="whatsapp-chat">
        <i class="fab fa-whatsapp"></i> Butuh bantuan? Chat dengan kami
    </a>

    <a href="index.php" class="back-link">← Kembali ke Halaman Utama</a>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>