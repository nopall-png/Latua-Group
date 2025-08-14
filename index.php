<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
include 'includes/db_connect.php';
include 'includes/header.php';

// Ambil SEMUA gambar hero yang aktif
$hero_images_data = [];
try {
    $stmt_hero = $pdo->query("SELECT image_path FROM hero_images WHERE is_active = 1 ORDER BY uploaded_at ASC");
    $hero_images_data = $stmt_hero->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $hero_images_data = [];
    error_log("Error fetching hero images: " . $e->getMessage());
}

// Jika tidak ada gambar hero aktif, gunakan gambar default
if (empty($hero_images_data)) {
    $hero_images_data[] = 'default_hero.jpg';
}

// Daftar Tipe Properti untuk form pencarian
$property_types_search = [
    "Rumah", "Tanah", "Apartemen", "Condotel", "Gedung", "Gudang", "Hotel", "Kantor",
    "Kavling", "Kios", "Komersial", "Kost", "Pabrik", "Ruang Usaha", "Ruko", "Rumah Kost"
];

// Ambil daftar provinsi dari database
$provinces_search = [];
try {
    $provinces_stmt_search = $pdo->query("SELECT id, name FROM provinces ORDER BY name ASC");
    $provinces_search = $provinces_stmt_search->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $provinces_search = [];
    error_log("Error fetching provinces: " . $e->getMessage());
}

// Ambil daftar kota/kabupaten dari database untuk di-cache di JS
$regencies_data_js_search = [];
try {
    $regencies_stmt_search = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name ASC");
    while ($row = $regencies_stmt_search->fetch(PDO::FETCH_ASSOC)) {
        $regencies_data_js_search[$row['province_name']][] = $row['regency_name'];
    }
} catch (PDOException $e) {
    $regencies_data_js_search = [];
    error_log("Error fetching regencies: " . $e->getMessage());
}

// Ambil properti terbaru
$latest_properties = [];
try {
    $stmt_latest = $pdo->query("
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
        ORDER BY 
            p.created_at DESC 
        LIMIT 6
    ");
    $latest_properties = $stmt_latest->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latest_properties = [];
    error_log("Error fetching latest properties: " . $e->getMessage());
}

// Ambil properti dijual
$sale_properties = [];
try {
    $stmt_sale = $pdo->prepare("
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
    ");
    $stmt_sale->execute(['for_sale']);
    $sale_properties = $stmt_sale->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sale_properties = [];
    error_log("Error fetching sale properties: " . $e->getMessage());
}

// Ambil properti disewakan
$rent_properties = [];
try {
    $stmt_rent = $pdo->prepare("
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
        LIMIT 3
    ");
    $stmt_rent->execute(['for_rent']);
    $rent_properties = $stmt_rent->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rent_properties = [];
    error_log("Error fetching rent properties: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Asia One Property</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #f8f9fa;
            color: #000000;
            line-height: 1.6;
            width: 100%;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #f8f9fa;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            background-color: #334894;
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #000000;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 60px;
            margin-right: 10px;
        }

        .navbar-brand:hover {
            background-color: #4a5fb3;
            color: #ffffff;
        }

        .nav-item {
            font-size: 0.85rem;
            color: #ffffff;
            background-color: #334894;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            border: 1px solid #000000;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .nav-item:hover {
            background-color: #4a5fb3;
            color: #ffffff;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 0;
            width: 100%;
            margin: 0;
            left: 0;
        }

        .hero-content {
            color: #ffffff;
            text-align: left;
            width: 100%;
            max-width: 100%;
            padding: 20px;
        }

        /* Search Form */
        .search-form-container {
            background: rgba(135, 206, 235, 0.25);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5.1px);
            -webkit-backdrop-filter: blur(5.1px);
            border: 1px solid rgba(135, 206, 235, 0.57);
            padding: 20px;
            color: #ffffff;
            width: 100%;
            max-width: 100%;
        }

        .property-search-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-label-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label {
            font-size: 1.1rem;
            font-weight: 400;
            color: #ffffff;
            margin: 0;
            white-space: nowrap;
            font-family: 'Lato', sans-serif;
        }

        .radio-group {
            display: flex;
            gap: 10px;
            align-items: center;
            background: rgba(135, 206, 235, 0.2);
            padding: 5px 10px;
            border: 1px solid rgba(135, 206, 235, 0.5);
            border-radius: 4px;
        }

        .radio-label {
            color: #ffffff;
            font-size: 1rem;
            cursor: pointer;
            font-family: 'Lato', sans-serif;
        }

        .form-input {
            padding: 10px;
            border: 1px solid rgba(135, 206, 235, 0.5);
            border-radius: 4px;
            font-size: 1rem;
            color: #000000;
            background: rgba(255, 255, 255, 0.9);
            width: 100%;
            max-width: none;
        }

        .form-input:disabled {
            background-color: #e0e0e0;
            cursor: not-allowed;
        }

        .price-group {
            display: flex;
            gap: 10px;
            align-items: center;
            width: 100%;
        }

        .price-label {
            font-size: 1rem;
            color: #ffffff;
            white-space: nowrap;
            font-family: 'Lato', sans-serif;
        }

        .search-button {
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 600 !important;
            background-color: #334894 !important;
            border: none !important;
            padding: 0.6em 1.4em !important;
            transition: background-color 0.3s ease, transform 0.1s ease !important;
        }

        .search-button:hover {
            background-color: #4a5fb3 !important;
            transform: scale(1.05) !important;
        }

        .search-button:active {
            background-color: #334894 !important;
            transform: scale(0.98) !important;
        }

        .view-all-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease, text-decoration 0.3s ease;
            display: inline-block;
            margin: 15px 0;
        }

        .view-all-text:hover {
            color: #34495e;
            text-decoration: underline;
        }

        .hero-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ffffff;
            padding: 10px 15px;
            display: inline-block;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            font-family: 'Lato', sans-serif;
        }

        /* Full Width Container */
        .full-width-container {
            width: 100%;
            margin: 0;
            padding: 0;
            background: inherit;
        }

        /* Section Styles */
        .property-section {
            text-align: center !important;
        }

        .section-subtitle {
            font-size: 1.1rem !important;
            margin: 20px auto 0 auto !important;
            text-align: center !important;
            padding: 5px 10px !important;
            border-radius: 4px !important;
            display: block !important;
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            color: #2c3e50 !important;
            text-transform: uppercase !important;
        }

        .section-divider {
            border: 0;
            height: 1px;
            background: #334894;
            margin: 20px 0;
        }

        /* Property Grid Containers */
        .property-grid-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 60px;
            text-align: center;
        }

        .property-grid {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 40px;
            padding-bottom: 10px;
            margin: 0 auto;
            justify-content: flex-start;
            scroll-snap-type: x mandatory;
        }

        .property-grid-wrapper {
            display: inline-flex;
            width: auto;
            justify-content: flex-start;
        }

        .property-grid::-webkit-scrollbar {
            display: none;
        }

        .nav-prev, .nav-next, .latest-nav-prev, .latest-nav-next, .sale-nav-prev, .sale-nav-next, .rent-nav-prev, .rent-nav-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.8);
            color: #2c3e50;
            border: 1px solid #e0e0e0;
            padding: 0;
            cursor: pointer;
            z-index: 10;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-prev, .latest-nav-prev, .sale-nav-prev, .rent-nav-prev {
            left: 10px;
        }

        .nav-next, .latest-nav-next, .sale-nav-next, .rent-nav-next {
            right: 10px;
        }

        .nav-prev:hover, .nav-next:hover, .latest-nav-prev:hover, .latest-nav-next:hover, .sale-nav-prev:hover, .sale-nav-next:hover, .rent-nav-prev:hover, .rent-nav-next:hover {
            background: rgba(255, 255, 255, 1);
            color: #2c3e50;
        }

        /* WhatsApp Chat Button */
        .whatsapp-chat {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(45deg, #10C65A, #0D994B);
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(16, 198, 90, 0.4);
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            z-index: 9999;
        }

        .whatsapp-chat i {
            font-size: 24px;
        }

        .whatsapp-chat:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(16, 198, 90, 0.6);
        }

        /* Property Card */
        .property-card {
            background: #ffffff;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
            max-width: 300px;
            min-width: 280px;
            margin: 20px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            flex: 0 0 auto;
            scroll-snap-align: center;
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
        }

        .price-overlay {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: #ffffff;
            color: #2c3e50;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: bold;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
            display: inline-block;
            line-height: 1.2;
            white-space: nowrap;
        }

        .price-text {
            font-size: 0.9rem;
            font-weight: bold;
            font-family: 'Arial', sans-serif;
            margin: 0;
            letter-spacing: 0.3px;
            color: #2c3e50;
        }

        .property-card-content {
            padding: 12px;
            text-align: left;
        }

        .property-title {
            font-size: 1.4rem;
            color: #2c3e50;
            margin-bottom: 8px;
            text-align: left;
            font-weight: 600;
            font-family: 'Lato', sans-serif;
        }

        .property-details {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: flex-start;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #333;
            font-size: 0.85rem;
            background: #f8f9fa;
            padding: 4px 6px;
            border-radius: 5px;
            text-align: left;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .detail-value {
            font-weight: bold;
            color: #2c3e50;
            font-family: 'Arial', sans-serif;
        }

        .detail-item i {
            color: #2c3e50;
            font-size: 0.85rem;
        }

        /* Footer Styles */
        .asiaone-footer {
            background-color: #f8f9fa;
            padding: 30px 0;
            color: #333;
            font-family: 'Arial', sans-serif;
            border-top: 1px solid #e0e0e0;
        }

        .footer-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            flex-wrap: wrap;
        }

        .footer-section {
            flex: 1;
            min-width: 180px;
            text-align: center;
        }

        .footer-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #00C4CC;
            color: white;
            padding: 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.1s;
            margin-bottom: 10px;
        }

        .footer-button:hover {
            background-color: #009DA6;
            transform: scale(1.05);
        }

        .footer-button i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .footer-button span {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .footer-button p {
            font-size: 12px;
            margin: 0;
            color: #fff;
        }

        .sell-rent-button {
            background-color: #00C4CC;
        }

        .sell-rent-button:hover {
            background-color: #009DA6;
            transform: scale(1.05);
        }

        .call-center-button, .whatsapp-button, .email-button {
            padding: 15px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            display: block;
            margin-top: 5px;
            transition: background-color 0.3s, transform 0.1s;
        }

        .call-center-button {
            background-color: #FFC107;
        }

        .call-center-button:hover {
            background-color: #FFA000;
            transform: scale(1.05);
        }

        .whatsapp-button {
            background-color: #4CAF50;
        }

        .whatsapp-button:hover {
            background-color: #45A049;
            transform: scale(1.05);
        }

        .email-button {
            background-color: #D44638;
        }

        .email-button:hover {
            background-color: #B4392C;
            transform: scale(1.05);
        }

        .jam-operasional {
            background-color: transparent;
            padding: 15px;
        }

        .jam-operasional p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .jam-operasional .time {
            font-weight: bold;
            color: #333;
        }

        .copyright {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        /* Media Queries untuk Responsif */
        @media (min-width: 768px) {
            .hero-section {
                min-height: 500px;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .form-label {
                font-size: 1.2rem;
            }

            .property-image-container {
                height: 250px;
            }

            .property-title {
                font-size: 1.6rem;
            }

            .section-subtitle {
                font-size: 1.4rem !important;
            }

            .property-grid {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                overflow-x: hidden;
                gap: 40px;
            }

            .property-grid-wrapper {
                display: flex;
                flex-wrap: wrap;
                width: 100%;
                justify-content: center;
            }

            .property-card {
                max-width: 320px;
                min-width: 300px;
                width: 100%;
                margin: 20px;
            }

            .nav-prev, .nav-next, .latest-nav-prev, .latest-nav-next, .sale-nav-prev, .sale-nav-next, .rent-nav-prev, .rent-nav-next {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .footer-container {
                flex-direction: column;
                text-align: center;
            }

            .footer-button, .call-center-button, .whatsapp-button, .email-button {
                width: 100%;
                box-sizing: border-box;
            }

            .hero-section {
                min-height: 50vh;
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .form-label {
                font-size: 1rem;
            }

            .property-image-container {
                height: 130px;
            }

            .section-subtitle {
                font-size: 1.1rem !important;
            }

            .property-grid {
                display: flex;
                overflow-x: auto;
                scroll-behavior: smooth;
                gap: 40px;
                justify-content: flex-start;
                padding: 0 15px;
                scroll-snap-type: x mandatory;
            }

            .property-grid-wrapper {
                display: inline-flex;
                width: auto;
                justify-content: flex-start;
            }

            .property-card {
                max-width: 240px;
                min-width: 240px;
                flex: 0 0 240px;
                margin: 20px 20px;
                scroll-snap-align: center;
                overflow: hidden;
            }

            .nav-prev, .nav-next, .latest-nav-prev, .latest-nav-next, .sale-nav-prev, .sale-nav-next, .rent-nav-prev, .rent-nav-next {
                width: 30px;
                height: 30px;
                font-size: 1rem;
            }

            .detail-item {
                font-size: 0.7rem;
                padding: 4px 6px;
                white-space: nowrap;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .detail-item i {
                font-size: 0.7rem;
            }

            .detail-value {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
<div class="hero-section" id="heroSection">
    <div class="hero-content">
        <div class="search-form-container">
            <form action="search_results.php" method="GET" class="property-search-form">
                <div class="form-section">
                    <h1 class="hero-title">Temukan Properti Idaman Bersama Kami</h1>
                    <div class="form-label-group">
                        <h3 class="form-label">Listing</h3>
                        <div class="radio-group">
                            <input type="radio" name="listing_type" value="for_sale" id="for_sale_search" checked>
                            <label for="for_sale_search" class="radio-label">Dijual</label>
                            <input type="radio" name="listing_type" value="for_rent" id="for_rent_search">
                            <label for="for_rent_search" class="radio-label">Disewakan</label>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h3 class="form-label">Tipe Properti</h3>
                    <select name="tipe_properti" id="tipe_properti_search" class="form-input">
                        <option value="">Semua</option>
                        <?php foreach ($property_types_search as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-section">
                    <h3 class="form-label">Lokasi</h3>
                    <select name="province" id="province_search" class="form-input">
                        <option value="">Semua</option>
                        <?php foreach ($provinces_search as $province_data): ?>
                            <option value="<?php echo htmlspecialchars($province_data['name']); ?>"><?php echo htmlspecialchars($province_data['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-section">
                    <h3 class="form-label">Harga</h3>
                    <div class="price-group">
                        <label for="harga_min" class="price-label">Rp.</label>
                        <input type="text" name="harga_min" id="harga_min" class="form-input" placeholder="Harga Min">
                        <label for="harga_max" class="price-label">Rp.</label>
                        <input type="text" name="harga_max" id="harga_max" class="form-input" placeholder="Harga Maks">
                    </div>
                </div>
                <div class="form-section">
                    <button type="submit" class="btn btn-lg search-button">CARI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<a href="https://wa.me/628111440205" class="whatsapp-chat" target="_blank">
    <i class="fab fa-whatsapp"></i> Butuh bantuan?
</a>

<div class="full-width-container">
    <div class="property-section">
        <h3 class="section-subtitle">Properti Terbaru Diunggah</h3>
        <div class="property-grid-container">
            <?php if (count($latest_properties) > 1): ?>
                <button class="nav-prev latest-nav-prev" onclick="scrollProperties('latest-grid', -200)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            <?php endif; ?>
            <div class="property-grid" id="latest-grid">
                <?php if (empty($latest_properties)): ?>
                    <p>Tidak ada properti yang ditemukan.</p>
                <?php else: ?>
                    <div class="property-grid-wrapper">
                        <?php foreach ($latest_properties as $property): ?>
                            <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                                <div class='property-card'>
                                    <div class="property-image-container">
                                        <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                        <div class="price-overlay">
                                            <p class="price-text">Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="property-card-content">
                                        <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                        <div class="property-details">
                                            <div class="detail-item">
                                                <i class="fa-solid fa-expand"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-house"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-bed"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                                <i class="fa-solid fa-bath"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (count($latest_properties) > 1): ?>
                <button class="nav-next latest-nav-next" onclick="scrollProperties('latest-grid', 200)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <hr class="section-divider">

    <div class="property-section">
        <h3 class="section-subtitle">Properti Dijual</h3>
        <div class="property-grid-container">
            <?php if (count($sale_properties) > 1): ?>
                <button class="nav-prev sale-nav-prev" onclick="scrollProperties('sale-grid', -200)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            <?php endif; ?>
            <div class="property-grid" id="sale-grid">
                <?php if (empty($sale_properties)): ?>
                    <p>Tidak ada properti yang ditemukan.</p>
                <?php else: ?>
                    <div class="property-grid-wrapper">
                        <?php foreach ($sale_properties as $property): ?>
                            <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                                <div class='property-card'>
                                    <div class="property-image-container">
                                        <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                        <div class="price-overlay">
                                            <p class="price-text">Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="property-card-content">
                                        <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                        <div class="property-details">
                                            <div class="detail-item">
                                                <i class="fa-solid fa-expand"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-house"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-bed"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                                <i class="fa-solid fa-bath"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (count($sale_properties) > 1): ?>
                <button class="nav-next sale-nav-next" onclick="scrollProperties('sale-grid', 200)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="view-all-link">
            <a href="user/sale_properties.php" class="view-all-text">Lihat Semua Properti Dijual</a>
        </div>
    </div>

    <hr class="section-divider">

    <div class="property-section">
        <h3 class="section-subtitle">Properti Disewakan</h3>
        <div class="property-grid-container">
            <?php if (count($rent_properties) > 1): ?>
                <button class="nav-prev rent-nav-prev" onclick="scrollProperties('rent-grid', -200)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            <?php endif; ?>
            <div class="property-grid" id="rent-grid">
                <?php if (empty($rent_properties)): ?>
                    <p>Tidak ada properti yang ditemukan.</p>
                <?php else: ?>
                    <div class="property-grid-wrapper">
                        <?php foreach ($rent_properties as $property): ?>
                            <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                                <div class='property-card'>
                                    <div class="property-image-container">
                                        <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                        <div class="price-overlay">
                                            <p class="price-text">Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="property-card-content">
                                        <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                        <div class="property-details">
                                            <div class="detail-item">
                                                <i class="fa-solid fa-expand"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-house"></i><span class="detail-value"><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</span>
                                                <i class="fa-solid fa-bed"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                                <i class="fa-solid fa-bath"></i><span class="detail-value"><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (count($rent_properties) > 1): ?>
                <button class="nav-next rent-nav-next" onclick="scrollProperties('rent-grid', 200)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="view-all-link">
            <a href="user/rent_properties.php" class="view-all-text">Lihat Semua Properti Disewakan</a>
        </div>
    </div>
</div>

<?php
// Dapatkan waktu saat ini (WIB)
date_default_timezone_set('Asia/Jakarta');
$currentTime = date('H:i');
$currentDay = date('l');
$currentDate = date('d F Y');

// Tentukan status operasional
$operatingHours = [
    'Monday' => ['09:00', '17:00'],
    'Tuesday' => ['09:00', '17:00'],
    'Wednesday' => ['09:00', '17:00'],
    'Thursday' => ['09:00', '17:00'],
    'Friday' => ['09:00', '17:00'],
    'Saturday' => ['09:00', '14:00'],
    'Sunday' => ['Tutup', 'Tutup']
];

$hours = $operatingHours[$currentDay];
$isOpen = false;

if ($hours[0] !== 'Tutup') {
    $openTime = strtotime($hours[0]);
    $closeTime = strtotime($hours[1]);
    $currentTimeStamp = strtotime($currentTime);

    $isOpen = ($currentTimeStamp >= $openTime && $currentTimeStamp < $closeTime);
}
?>

<footer class="asiaone-footer">
    <div class="footer-container">
        <div class="footer-section">
            <a href="/LatuaGroup/agen.php" class="footer-button">
                <i class="fas fa-users"></i>
                <span>CARI AGEN</span>
                <p>"Agen-agen kami akan membantu menemukan properti idaman anda"</p>
            </a>
        </div>

        <div class="footer-section">
            <a href="#" class="footer-button">
                <i class="fas fa-home"></i>
                <span>CARI PROPERTI</span>
                <p>Cari Rumah, Apartemen, Ruko/Komersil, Tanah, atau Gudang</p>
            </a>
        </div>

        <div class="footer-section">
            <a href="/LatuaGroup/sell_property.php" class="footer-button sell-rent-button">
                <i class="fas fa-key"></i>
                <span>INGIN MENJUAL / MENYEWAKAN?</span>
                <p>ISI FORM ></p>
            </a>
        </div>

        <div class="footer-section jam-operasional">
            <p>JAM OPERASIONAL</p>
            <p>Senin - Jumat: <span class="time">09:00 - 17:00</span></p>
            <p>Sabtu: <span class="time">09:00 - 14:00</span></p>
            <p>Minggu & Hari Libur: <span class="time">Tutup</span></p>
            <?php if ($isOpen): ?>
                <p style="color: green; font-weight: bold;">Sedang Buka Sekarang</p>
            <?php else: ?>
                <p style="color: red; font-weight: bold;">Tutup Sekarang</p>
            <?php endif; ?>
        </div>

        <div class="footer-section">
            <p>CALL CENTER</p>
            <a href="tel:+628111440205" class="call-center-button">
                <i class="fas fa-phone"></i> 0811-1440-205
            </a>
            <a href="https://wa.me/628111440205" class="whatsapp-button">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="mailto:latuealand@gmail.com" class="email-button">
                <i class="fas fa-envelope"></i> Email
            </a>
        </div>
    </div>
    <div class="copyright">
        <p>© 2025 Property Web. All rights reserved.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Hero Section
    const heroSection = document.getElementById('heroSection');
    const heroImages = <?php echo json_encode(array_map(function($path) { return 'Uploads/hero/' . $path; }, $hero_images_data)); ?> || [];

    function setHeroImage(index) {
        if (heroImages.length > 0 && heroImages[index]) {
            heroSection.style.backgroundImage = `url('${heroImages[index]}')`;
        } else {
            heroSection.style.backgroundImage = `url('Uploads/hero/default_hero.jpg')`;
        }
    }

    if (heroImages.length > 1) {
        let currentIndex = 0;
        setHeroImage(currentIndex);
        setInterval(() => {
            currentIndex = (currentIndex + 1) % heroImages.length;
            setHeroImage(currentIndex);
        }, 5000);
    } else {
        setHeroImage(0);
    }

    // Dropdown Lokasi Pencarian
    const provinceSearchSelect = document.getElementById('province_search');
    const regencySearchSelect = document.getElementById('regency_search');
    const regenciesSearchData = <?php echo json_encode($regencies_data_js_search); ?> || {};

    provinceSearchSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        regencySearchSelect.innerHTML = '<option value="">Semua</option>';
        regencySearchSelect.disabled = true;

        if (selectedProvince && regenciesSearchData[selectedProvince] && Array.isArray(regenciesSearchData[selectedProvince])) {
            regenciesSearchData[selectedProvince].forEach(regency => {
                const option = document.createElement('option');
                option.value = regency;
                option.textContent = regency;
                regencySearchSelect.appendChild(option);
            });
            regencySearchSelect.disabled = false;
        }
    });

    // Scroll Properti
    window.scrollProperties = function(id, distance) {
        const container = document.getElementById(id);
        if (container) {
            container.scrollBy({ left: distance, behavior: 'smooth' });
        }
    };

    // Format input harga di form pencarian
    const priceInputs = [document.getElementById('harga_min'), document.getElementById('harga_max')];
    priceInputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9,]/g, '');
                if (value.includes(',')) {
                    let parts = value.split(',');
                    if (parts[1] && parts[1].length > 2) {
                        parts[1] = parts[1].slice(0, 2);
                    }
                    value = parts.join(',');
                }
                e.target.value = value;
            });
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>