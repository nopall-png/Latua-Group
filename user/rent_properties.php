<?php
include '../includes/db_connect.php';
include '../includes/header.php';

// Konfigurasi Paginasi
$properties_per_page = 9;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $properties_per_page;

$property_type = 'for_rent';

// 1. Hitung Total Properti
$count_stmt = $pdo->prepare("SELECT COUNT(id) FROM properties WHERE property_type = ?");
$count_stmt->execute([$property_type]);
$total_properties = $count_stmt->fetchColumn();

// 2. Hitung Total Halaman
$total_pages = ceil($total_properties / $properties_per_page);

// 3. Ambil Properti untuk Halaman Saat Ini
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.price, 
        p.province, 
        p.regency, 
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
    WHERE 
        p.property_type = ?
    ORDER BY 
        p.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $property_type, PDO::PARAM_STR);
$stmt->bindValue(2, $properties_per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties for Rent</title>
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
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            gap: 5px;
        }
        
        .page-link, .prev-next {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #334894;
            transition: all 0.3s ease;
        }
        
        .page-link:hover, .prev-next:hover {
            background-color: #334894;
            color: white;
        }
        
        .active {
            background-color: #334894;
            color: white;
            border-color: #334894;
        }
        
        .page-dots {
            padding: 8px 12px;
        }
        
        /* No Properties Message */
        .no-properties {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px 0;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Properties for Rent</h1>
    <div class="property-grid">
        <?php if (empty($properties)): ?>
            <p class="no-properties">Tidak ada properti untuk disewakan saat ini.</p>
        <?php else: ?>
            <?php foreach ($properties as $property): ?>
                <a href="../detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                    <div class="property-card">
                        <div class="property-image-container">
                            <img src="../Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                            <div class="price-overlay">
                                <p class="price-text">Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="property-card-content">
                            <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
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

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="page-link prev-next">&laquo; Prev</a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            if ($start_page > 1) {
                echo '<a href="?page=1" class="page-link">1</a>';
                if ($start_page > 2) {
                    echo '<span class="page-dots">...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                    <span class="page-dots">...</span>
                <?php endif; ?>
                <a href="?page=<?php echo $total_pages; ?>" class="page-link"><?php echo $total_pages; ?></a>
            <?php endif; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="page-link prev-next">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>