<?php
include 'includes/db_connect.php';
include 'includes/header.php';

// Ambil SEMUA gambar hero yang aktif
$hero_images_data = [];
try {
    $stmt_hero = $pdo->query("SELECT image_path FROM hero_images WHERE is_active = 1 ORDER BY uploaded_at ASC");
    $hero_images_data = $stmt_hero->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $hero_images_data = [];
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
}

// Ambil daftar kota/kabupaten dari database untuk di-cache di JS
$regencies_data_js_search = [];
try {
    $regencies_stmt_search = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name ASC");
    while($row = $regencies_stmt_search->fetch(PDO::FETCH_ASSOC)) {
        $regencies_data_js_search[$row['province_name']][] = $row['regency_name'];
    }
} catch (PDOException $e) {
    $regencies_data_js_search = [];
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
    $latest_properties = $stmt_latest->fetchAll();
} catch (PDOException $e) {
    $latest_properties = [];
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
        LIMIT 3
    ");
    $stmt_sale->execute(['for_sale']);
    $sale_properties = $stmt_sale->fetchAll();
} catch (PDOException $e) {
    $sale_properties = [];
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
    $rent_properties = $stmt_rent->fetchAll();
} catch (PDOException $e) {
    $rent_properties = [];
}
?>

<div class="hero-section" id="heroSection">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Temukan Properti Idaman Bersama Kami</h1>
        <div class="search-form-container">
            <form action="search_results.php" method="GET" class="property-search-form">
                <div class="radio-group">
                    <input type="radio" name="listing_type" value="for_sale" id="for_sale_search" checked>
                    <label for="for_sale_search">Dijual</label>
                    <input type="radio" name="listing_type" value="for_rent" id="for_rent_search">
                    <label for="for_rent_search">Disewakan</label>
                </div>
                <select name="tipe_properti" id="tipe_properti_search">
                    <option value="">Semua Tipe Properti</option>
                    <?php foreach ($property_types_search as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="province" id="province_search">
                    <option value="">Pilih Provinsi</option>
                    <?php foreach ($provinces_search as $province_data): ?>
                        <option value="<?php echo htmlspecialchars($province_data['name']); ?>"><?php echo htmlspecialchars($province_data['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="regency" id="regency_search" disabled>
                    <option value="">Pilih Kota/Kabupaten</option>
                </select>
                <input type="text" name="district_or_area" id="district_or_area_search" placeholder="Kecamatan/Area (misal: Kemang)">
                <div class="price-range">
                    <input type="number" name="harga_min" id="harga_min" placeholder="Rp. Harga Min">
                    <input type="number" name="harga_max" id="harga_max" placeholder="Rp. Harga Maks">
                </div>
                <button type="submit">CARI</button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <h2 class="section-title">Semua Properti Tersedia</h2>

    <div class="property-section">
        <h3 class="section-subtitle">Properti Terbaru Diunggah</h3>
        <div class="property-swipe-container">
            <button class="nav-prev swipe-btn" onclick="scrollProperties('swipe-latest', -250)">&lt;</button>
            <div class="property-swipe" id="swipe-latest">
                <?php if (empty($latest_properties)): ?>
                    <p>Tidak ada properti terbaru yang ditemukan.</p>
                <?php else: ?>
                    <?php foreach ($latest_properties as $property): ?>
                        <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                            <div class='property-card'>
                                <div class="property-image-container">
                                    <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                    <div class="price-overlay">
                                        <p>Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                                <div class="property-card-content">
                                    <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                    <div class="property-details">
                                        <div class="detail-item">
                                            <i class="fa-solid fa-expand"></i>
                                            <span><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m2</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa-solid fa-house"></i>
                                            <span><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m2</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa-solid fa-bed"></i>
                                            <span><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa-solid fa-bath"></i>
                                            <span><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="nav-next swipe-btn" onclick="scrollProperties('swipe-latest', 250)">&gt;</button>
        </div>
    </div>

    <hr class="section-divider">

    <div class="property-section">
        <h3 class="section-subtitle">Properti Dijual</h3>
        <div class="property-grid">
            <?php if (empty($sale_properties)): ?>
                <p>Tidak ada properti untuk dijual yang ditemukan.</p>
            <?php else: ?>
                <?php foreach ($sale_properties as $property): ?>
                    <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                        <div class='property-card'>
                            <div class="property-image-container">
                                <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                <div class="price-overlay">
                                    <p>Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="property-card-content">
                                <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                <div class="property-details">
                                    <div class="detail-item">
                                        <i class="fa-solid fa-expand"></i>
                                        <span><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m2</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-house"></i>
                                        <span><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m2</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-bed"></i>
                                        <span><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-bath"></i>
                                        <span><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="view-all-link">
            <a href="user/sale_properties.php">Lihat Properti Dijual Lainnya &rarr;</a>
        </div>
    </div>

    <hr class="section-divider">

    <div class="property-section">
        <h3 class="section-subtitle">Properti Disewakan</h3>
        <div class="property-grid">
            <?php if (empty($rent_properties)): ?>
                <p>Tidak ada properti untuk disewakan yang ditemukan.</p>
            <?php else: ?>
                <?php foreach ($rent_properties as $property): ?>
                    <a href='detail_property.php?id=<?php echo $property['id']; ?>' class='property-card-link'>
                        <div class='property-card'>
                            <div class="property-image-container">
                                <img src='Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>' alt='<?php echo htmlspecialchars($property['title']); ?>'>
                                <div class="price-overlay">
                                    <p>Rp <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="property-card-content">
                                <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                <div class="property-details">
                                    <div class="detail-item">
                                        <i class="fa-solid fa-expand"></i>
                                        <span><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m2</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-house"></i>
                                        <span><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m2</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-bed"></i>
                                        <span><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa-solid fa-bath"></i>
                                        <span><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="view-all-link">
            <a href="user/rent_properties.php">Lihat Properti Disewakan Lainnya &rarr;</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Hero Section
    const heroSection = document.getElementById('heroSection');
    const heroImages = <?php echo json_encode(array_map(function($path) { return 'Uploads/hero/' . $path; }, $hero_images_data)); ?>;

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
    const regenciesSearchData = <?php echo json_encode($regencies_data_js_search); ?>;

    provinceSearchSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        regencySearchSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        regencySearchSelect.disabled = true;

        if (selectedProvince && regenciesSearchData[selectedProvince]) {
            regenciesSearchData[selectedProvince].forEach(regency => {
                const option = document.createElement('option');
                option.value = regency;
                option.textContent = regency;
                regencySearchSelect.appendChild(option);
            });
            regencySearchSelect.disabled = false;
        }
    });

    // Scroll Properti Terbaru (Swipe)
    window.scrollProperties = function(id, distance) {
        const container = document.getElementById(id);
        if (container) {
            container.scrollBy({ left: distance, behavior: 'smooth' });
        }
    };
});
</script>

<?php include 'includes/footer.php'; ?>