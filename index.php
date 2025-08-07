<?php
include 'includes/db_connect.php';
include 'includes/header.php';

// Ambil SEMUA gambar hero yang aktif
$hero_images_data = [];
$stmt_hero = $pdo->query("SELECT image_path FROM hero_images WHERE is_active = 1 ORDER BY uploaded_at ASC");
$hero_images_data = $stmt_hero->fetchAll(PDO::FETCH_COLUMN);

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
$provinces_stmt_search = $pdo->query("SELECT id, name FROM provinces ORDER BY name ASC");
$provinces_search = $provinces_stmt_search->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar kota/kabupaten dari database untuk di-cache di JS
$regencies_data_js_search = [];
$regencies_stmt_search = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name ASC");
while($row = $regencies_stmt_search->fetch(PDO::FETCH_ASSOC)) {
    $regencies_data_js_search[$row['province_name']][] = $row['regency_name'];
}
?>

<div class="hero-section" id="heroSection">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Temukan Properti Idaman Bersama Kami</h1>
        <div class="search-form-container">
            <form action="search_results.php" method="GET" class="property-search-form">
                <div class="radio-group">
                    <label><input type="radio" name="listing_type" value="for_sale" checked> Dijual</label>
                    <label><input type="radio" name="listing_type" value="for_rent"> Disewakan</label>
                </div>
                <label for="tipe_properti_search" class="sr-only">Tipe Properti</label>
                <select name="tipe_properti" id="tipe_properti_search">
                    <option value="">Semua Tipe Properti</option>
                    <?php foreach ($property_types_search as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="province_search" class="sr-only">Provinsi</label>
                <select name="province" id="province_search">
                    <option value="">Pilih Provinsi</option>
                    <?php foreach ($provinces_search as $province_data): ?>
                        <option value="<?php echo htmlspecialchars($province_data['name']); ?>"><?php echo htmlspecialchars($province_data['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="regency_search" class="sr-only">Kota/Kabupaten</label>
                <select name="regency" id="regency_search" disabled>
                    <option value="">Pilih Kota/Kabupaten</option>
                </select>
                <label for="district_or_area_search" class="sr-only">Kecamatan/Area</label>
                <input type="text" name="district_or_area" id="district_or_area_search" placeholder="Kecamatan/Area (misal: Kemang)">
                <div class="price-range">
                    <label for="harga_min" class="sr-only">Harga Min</label>
                    <input type="number" name="harga_min" id="harga_min" placeholder="Rp. Harga Min">
                    <label for="harga_max" class="sr-only">Harga Maks</label>
                    <input type="number" name="harga_max" id="harga_max" placeholder="Rp. Harga Maks">
                </div>
                <button type="submit">CARI</button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <h2>Semua Properti Tersedia</h2>

    <h3>Properti Terbaru Diunggah</h3>
    <div class="property-swipe-container">
        <button class="nav-prev">&lt;</button>
        <div class="property-swipe">
            <?php
            $stmt = $pdo->query("
                SELECT 
                    p.id, 
                    p.title, 
                    p.description, 
                    p.price, 
                    p.id_properti,
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
            while ($property = $stmt->fetch()) {
                echo "<a href='detail_property.php?id=" . $property['id'] . "' class='property-card-link'>";
                echo "<div class='property-card'>";
                echo "<img src='Uploads/" . htmlspecialchars($property['main_image_path'] ?? 'default.jpg') . "' alt='" . htmlspecialchars($property['title']) . "'>";
                echo "<h3>" . htmlspecialchars($property['title']) . "</h3>";
                echo "<p>" . htmlspecialchars(substr($property['description'], 0, 100)) . "...</p>";
                echo "<p>Harga: Rp" . number_format($property['price'], 0, ',', '.') . "</p>";
                echo "</div>";
                echo "</a>";
            }
            ?>
        </div>
        <button class="nav-next">&gt;</button>
    </div>

    <h3>Properti Dijual</h3>
    <div class="property-grid">
        <?php
        $stmt = $pdo->prepare("
            SELECT 
                p.id, 
                p.title, 
                p.description, 
                p.price, 
                p.id_properti,
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
            LIMIT 3
        ");
        $stmt->execute(['for_sale']);
        while ($property = $stmt->fetch()) {
            echo "<a href='detail_property.php?id=" . $property['id'] . "' class='property-card-link'>";
            echo "<div class='property-card'>";
            echo "<img src='Uploads/" . htmlspecialchars($property['main_image_path'] ?? 'default.jpg') . "' alt='" . htmlspecialchars($property['title']) . "'>";
            echo "<h3>" . htmlspecialchars($property['title']) . "</h3>";
            echo "<p>" . htmlspecialchars(substr($property['description'], 0, 100)) . "...</p>";
            echo "<p>Harga: Rp" . number_format($property['price'], 0, ',', '.') . "</p>";
            echo "</div>";
            echo "</a>";
        }
        ?>
    </div>
    <a href="user/sale_properties.php">Lihat Properti Dijual Lainnya</a>

    <h3>Properti Disewakan</h3>
    <div class="property-grid">
        <?php
        $stmt = $pdo->prepare("
            SELECT 
                p.id, 
                p.title, 
                p.description, 
                p.price, 
                p.id_properti,
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
            LIMIT 3
        ");
        $stmt->execute(['for_rent']);
        while ($property = $stmt->fetch()) {
            echo "<a href='detail_property.php?id=" . $property['id'] . "' class='property-card-link'>";
            echo "<div class='property-card'>";
            echo "<img src='Uploads/" . htmlspecialchars($property['main_image_path'] ?? 'default.jpg') . "' alt='" . htmlspecialchars($property['title']) . "'>";
            echo "<h3>" . htmlspecialchars($property['title']) . "</h3>";
            echo "<p>" . htmlspecialchars(substr($property['description'], 0, 100)) . "...</p>";
            echo "<p>Harga: Rp" . number_format($property['price'], 0, ',', '.') . "</p>";
            echo "</div>";
            echo "</a>";
        }
        ?>
    </div>
    <a href="user/rent_properties.php">Lihat Properti Disewakan Lainnya</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroSection = document.getElementById('heroSection');
    const heroImages = <?php echo json_encode(array_map(function($path) { return 'Uploads/hero/' . $path; }, $hero_images_data)); ?>;

    // Debugging: Log the hero images to the console
    console.log('Hero Images:', heroImages);

    // Fungsi untuk mengatur gambar hero
    function setHeroImage(index) {
        if (heroImages.length > 0 && heroImages[index]) {
            heroSection.style.backgroundImage = `url('${heroImages[index]}')`;
        } else {
            heroSection.style.backgroundImage = `url('Uploads/hero/default_hero.jpg')`;
        }
        heroSection.style.backgroundSize = 'cover';
        heroSection.style.backgroundRepeat = 'no-repeat';
        heroSection.style.backgroundPosition = 'center';
    }

    // Animasi ganti gambar setiap 5 detik jika lebih dari 1 gambar
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

    // JavaScript untuk Cascading Dropdown Lokasi di Search Form
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
});
</script>

<?php include 'includes/footer.php'; ?>