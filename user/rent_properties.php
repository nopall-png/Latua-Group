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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties for Rent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            background-color: #f8f9fa;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 3px;
            padding: 12px;
            box-sizing: border-box;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin: 0 0 12px 0;
            font-size: 1.6rem;
            font-weight: 700;
        }
        
        /* Grid Wrapper */
        .grid-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        /* Property Grid */
        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 340px)) !important;
            gap: 4px;
            margin: 6px 0;
            justify-content: center;
            width: 100%;
        }
        
        /* Property Card */
        .property-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }
        
        .property-card {
            background: #FFFFFF;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            height: 100%;
            display: flex;
            flex-direction: column;
            max-width: 100% !important;
        }
        
        .property-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        
        .property-image-container {
            position: relative;
            width: 100%;
            height: 180px !important;
            overflow: hidden;
        }
        
        .property-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease;
        }
        
        .property-card:hover .property-image-container img {
            transform: scale(1.02);
        }
        
        /* Price Overlay */
        .price-overlay {
            position: absolute;
            bottom: 6px;
            left: 6px;
            background: #FFFFFF;
            color: #000000;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        
        .price-text {
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0;
            color: #000000;
            white-space: nowrap;
        }
        
        /* Card Content */
        .property-card-content {
            padding: 10px;
            flex-grow: 1;
        }
        
        .property-title {
            font-size: 1rem;
            color: #000000;
            margin: 0 0 8px 0;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .property-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-top: 6px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            color: #555;
            background: #f8f9fa;
            padding: 5px 8px;
            border-radius: 5px;
        }
        
        .detail-item i {
            color: #334894;
            font-size: 0.8rem;
            min-width: 12px;
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
            margin-top: 16px;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .page-link, .prev-next {
            padding: 5px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #334894;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .page-link:hover, .prev-next:hover {
            background-color: #334894;
            color: white;
            border-color: #334894;
        }
        
        .active {
            background-color: #334894;
            color: white;
            border-color: #334894;
        }
        
        .page-dots {
            padding: 5px 8px;
            font-size: 0.85rem;
            color: #555;
        }
        
        /* WhatsApp Chat Button */
        .whatsapp-chat {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: #25D366;
            color: white;
            padding: 8px 12px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 10px rgba(37, 211, 102, 0.3);
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            z-index: 1000;
            font-size: 0.85rem;
        }
        .whatsapp-chat i {
            font-size: 1rem;
        }
        .whatsapp-chat:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
        }
        
        /* No Properties Message */
        .no-properties {
            text-align: center;
            grid-column: 1 / -1;
            padding: 20px 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .container {
                margin-top: 3px;
                padding: 4px;
            }
            
            h1 {
                font-size: 1.2rem;
                margin: 0 0 8px 0;
            }
            
            .grid-wrapper {
                display: flex;
                justify-content: center;
                width: 100%;
            }
            
            .property-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
                gap: 12px;
                margin: 8px 0;
            }
            
            .property-card {
                max-width: 90vw !important;
                margin: 0 auto !important;
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            }
            
            .property-image-container {
                height: 150px !important;
            }
            
            .property-title {
                font-size: 0.95rem;
                margin: 0 0 6px 0;
            }
            
            .property-details {
                grid-template-columns: 1fr;
                gap: 5px;
                margin-top: 5px;
            }
            
            .detail-item {
                font-size: 0.75rem;
                gap: 4px;
                padding: 4px 6px;
            }
            
            .detail-item i {
                font-size: 0.75rem;
                min-width: 10px;
            }
            
            .price-overlay {
                padding: 3px 6px;
                font-size: 0.75rem;
                bottom: 5px;
                left: 5px;
            }
            
            .price-text {
                font-size: 0.75rem;
            }
            
            .property-card-content {
                padding: 8px;
            }
            
            .whatsapp-chat {
                padding: 6px 10px;
                font-size: 0.8rem;
                bottom: 10px;
                right: 10px;
            }
            .whatsapp-chat i {
                font-size: 0.9rem;
            }
            
            .pagination {
                margin-top: 12px;
                gap: 3px;
            }
            .page-link, .prev-next {
                padding: 4px 6px;
                font-size: 0.75rem;
            }
            .page-dots {
                padding: 4px 6px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Properties for Rent</h1>
    <div class="grid-wrapper">
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
                                        <i class="fas fa-ruler-combined"></i>
                                        <span class="detail-value"><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-home"></i>
                                        <span class="detail-value"><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-bed"></i>
                                        <span class="detail-value"><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-bath"></i>
                                        <span class="detail-value"><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?> m²</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <a href="https://wa.me/62123456789" class="whatsapp-chat">
        <i class="fab fa-whatsapp"></i> Butuh bantuan? Chat
    </a>

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

            for ($i = $start_page; $i <= $end_page; $i++): ?>
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