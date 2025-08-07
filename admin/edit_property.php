<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Pastikan ada ID properti yang akan diedit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='admin-container'><p class='error-message'>ID Properti tidak valid.</p></div>";
    include '../includes/footer.php';
    exit();
}

$property_id = $_GET['id'];
$upload_messages = [];

// Proses form submission (UPDATE data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    // Sanitasi input harga: hapus titik dan ubah koma menjadi titik untuk parsing
    $price_input = str_replace(['.', ','], ['', '.'], $_POST['price']);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT, ['options' => ['default' => null]]);
    if ($price === false || $price === null || $price < 0 || $price > 99999999.99) {
        $upload_messages[] = "<span class='error-message'>Harga tidak valid. Harap masukkan angka positif hingga Rp 99.999.999,99.</span>";
        $stmt_fetch = $pdo->prepare("SELECT price FROM properties WHERE id = ?");
        $stmt_fetch->execute([$property_id]);
        $price = $stmt_fetch->fetchColumn(); // Fallback to existing price
    }
    $property_type = $_POST['property_type'];
    $id_properti = $_POST['id_properti'];
    $tipe_properti = $_POST['tipe_properti'];
    $luas_tanah = $_POST['luas_tanah'];
    $luas_bangunan = $_POST['luas_bangunan'];
    $arah_bangunan = $_POST['arah_bangunan'];
    $jenis_bangunan = $_POST['jenis_bangunan'];
    $jumlah_lantai = $_POST['jumlah_lantai'];
    $kamar_tidur = $_POST['kamar_tidur'];
    $kamar_pembantu = $_POST['kamar_pembantu'];
    $kamar_mandi = $_POST['kamar_mandi'];
    $daya_listrik = $_POST['daya_listrik'];
    $saluran_air = $_POST['saluran_air'];
    $jalur_telepon = $_POST['jalur_telepon'];
    $interior = $_POST['interior'];
    $garasi_parkir = $_POST['garasi_parkir'];
    $sertifikat = $_POST['sertifikat'];
    $view_count = $_POST['view_count'];
    $province = $_POST['province'] ?? '';
    $regency = $_POST['regency'] ?? '';
    $district_or_area = $_POST['district_or_area'] ?? '';
    $agent_id = !empty($_POST['agent_id']) && is_numeric($_POST['agent_id']) ? $_POST['agent_id'] : null;

    try {
        $stmt = $pdo->prepare("UPDATE properties SET
            title = ?, description = ?, price = ?, property_type = ?,
            id_properti = ?, tipe_properti = ?, luas_tanah = ?, luas_bangunan = ?, arah_bangunan = ?,
            jenis_bangunan = ?, jumlah_lantai = ?, kamar_tidur = ?, kamar_pembantu = ?, kamar_mandi = ?,
            daya_listrik = ?, saluran_air = ?, jalur_telepon = ?, interior = ?, garasi_parkir = ?,
            sertifikat = ?, view_count = ?,
            province = ?, regency = ?, district_or_area = ?, agent_id = ?
            WHERE id = ?");

        $params = [
            $title, $description, $price, $property_type,
            $id_properti, $tipe_properti, $luas_tanah, $luas_bangunan, $arah_bangunan,
            $jenis_bangunan, $jumlah_lantai, $kamar_tidur, $kamar_pembantu, $kamar_mandi,
            $daya_listrik, $saluran_air, $jalur_telepon, $interior, $garasi_parkir,
            $sertifikat, $view_count,
            $province, $regency, $district_or_area, $agent_id,
            $property_id
        ];

        error_log("Updating property ID $property_id with price: $price");
        $stmt->execute($params);
        $upload_messages[] = "<span class='success-message'>Properti berhasil diperbarui!</span>";
        header("Location: ../index.php?updated=" . time());
        exit();
    } catch (PDOException $e) {
        $upload_messages[] = "<span class='error-message'>Error memperbarui properti: " . $e->getMessage() . "</span>";
        error_log("Error updating property: " . $e->getMessage());
    }

    // Handle multiple image uploads
    if (isset($_FILES['images']['name']) && is_array($_FILES['images']['name']) && !empty(array_filter($_FILES['images']['name']))) {
        $target_dir = "../Uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $uploaded_count = 0;
        $max_files_to_add = 5;

        $stmt_count_current_images = $pdo->prepare("SELECT COUNT(*) FROM property_images WHERE property_id = ?");
        $stmt_count_current_images->execute([$property_id]);
        $current_image_count = $stmt_count_current_images->fetchColumn();

        foreach ($_FILES['images']['name'] as $key => $name) {
            if (!empty($name) && $_FILES['images']['error'][$key] == UPLOAD_ERR_OK) {
                if (($current_image_count + $uploaded_count) >= $max_files_to_add) {
                    $upload_messages[] = "<span style='color: orange;'>Maksimal total 5 gambar untuk properti ini. Beberapa gambar baru dilewati.</span>";
                    break;
                }

                $tmp_name = $_FILES['images']['tmp_name'][$key];
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                $new_file_name = uniqid('img_', true) . '.' . $file_extension;
                $upload_path = $target_dir . $new_file_name;

                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $img_stmt = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    $img_stmt->execute([$property_id, $new_file_name]);
                    $uploaded_count++;
                } else {
                    $upload_messages[] = "<span class='error-message'>Gagal memindahkan file upload: " . htmlspecialchars($name) . "</span>";
                }
            } elseif ($_FILES['images']['error'][$key] != UPLOAD_ERR_NO_FILE) {
                $upload_messages[] = "<span class='error-message'>Error upload " . htmlspecialchars($name) . ": " . $_FILES['images']['error'][$key] . "</span>";
            }
        }
        if ($uploaded_count > 0) {
            $upload_messages[] = "<span class='success-message'>Berhasil mengunggah " . $uploaded_count . " gambar baru.</span>";
        }
    }
}

// Ambil data properti
$stmt_fetch = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt_fetch->execute([$property_id]);
$property = $stmt_fetch->fetch();

if (!$property) {
    echo "<div class='admin-container'><p class='error-message'>Properti tidak ditemukan untuk diedit.</p></div>";
    include '../includes/footer.php';
    exit();
}

// Ambil gambar properti
$stmt_current_images = $pdo->prepare("SELECT id, image_path FROM property_images WHERE property_id = ? ORDER BY id ASC");
$stmt_current_images->execute([$property_id]);
$current_images = $stmt_current_images->fetchAll();

// Proses penghapusan gambar
if (isset($_GET['delete_image_id']) && is_numeric($_GET['delete_image_id'])) {
    $delete_image_id = $_GET['delete_image_id'];
    $stmt_get_image_path = $pdo->prepare("SELECT image_path FROM property_images WHERE id = ? AND property_id = ?");
    $stmt_get_image_path->execute([$delete_image_id, $property_id]);
    $image_to_delete = $stmt_get_image_path->fetchColumn();

    if ($image_to_delete) {
        try {
            $stmt_delete_image = $pdo->prepare("DELETE FROM property_images WHERE id = ?");
            $stmt_delete_image->execute([$delete_image_id]);
            $file_path = '../Uploads/' . $image_to_delete;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $upload_messages[] = "<span class='success-message'>Gambar berhasil dihapus.</span>";
            header("Location: edit_property.php?id=" . $property_id . "&msg=" . urlencode(strip_tags($upload_messages[count($upload_messages)-1])));
            exit();
        } catch (PDOException $e) {
            $upload_messages[] = "<span class='error-message'>Error menghapus gambar: " . $e->getMessage() . "</span>";
        }
    } else {
        $upload_messages[] = "<span class='error-message'>Gambar tidak ditemukan atau bukan milik properti ini.</span>";
    }
}

// Daftar Tipe Properti
$property_types = [
    "Apartemen", "Condotel", "Gedung", "Gudang", "Hotel", "Kantor",
    "Kavling", "Kios", "Komersial", "Kost", "Pabrik", "Ruang Usaha",
    "Ruko", "Rumah", "Rumah Kost", "Tanah"
];

// Ambil daftar provinsi
$provinces_stmt = $pdo->query("SELECT id, name FROM provinces ORDER BY name ASC");
$provinces = $provinces_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar kota/kabupaten
$regencies_data_js = [];
$regencies_stmt = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name ASC");
while($row = $regencies_stmt->fetch(PDO::FETCH_ASSOC)) {
    $regencies_data_js[$row['province_name']][] = $row['regency_name'];
}

// Ambil daftar agen
$stmt_agents = $pdo->query("SELECT id, name FROM agents ORDER BY name ASC");
$agents = $stmt_agents->fetchAll(PDO::FETCH_ASSOC);

// Format harga ke Rupiah untuk input
$formatted_price = number_format($property['price'], 2, ',', '.');
?>

<div class="admin-container">
    <h2>Edit Properti: <?php echo htmlspecialchars($property['title']); ?></h2>

    <?php foreach ($upload_messages as $msg): ?>
        <p><?php echo $msg; ?></p>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Judul Properti:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>

        <label for="id_properti">ID Properti (Contoh: 118536):</label>
        <input type="text" name="id_properti" value="<?php echo htmlspecialchars($property['id_properti']); ?>" required>

        <label for="property_type">Tipe Penawaran:</label>
        <select name="property_type" required>
            <option value="for_sale" <?php echo ($property['property_type'] == 'for_sale') ? 'selected' : ''; ?>>For Sale</option>
            <option value="for_rent" <?php echo ($property['property_type'] == 'for_rent') ? 'selected' : ''; ?>>For Rent</option>
        </select>

        <label for="tipe_properti">Tipe Properti:</label>
        <select name="tipe_properti" required>
            <option value="">Pilih Tipe Properti</option>
            <?php foreach ($property_types as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($property['tipe_properti'] == $type) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type); ?></option>
            <?php endforeach; ?>
        </select>

        <h3>Lokasi Properti:</h3>
        <label for="province">Provinsi:</label>
        <select name="province" id="provinceSelect" required>
            <option value="">Pilih Provinsi</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo htmlspecialchars($province['name']); ?>" <?php echo ($property['province'] == $province['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($province['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="regency">Kota/Kabupaten:</label>
        <select name="regency" id="regencySelect" required <?php echo empty($property['province']) ? 'disabled' : ''; ?>>
            <option value="">Pilih Kota/Kabupaten</option>
            <?php 
            if (!empty($property['province']) && isset($regencies_data_js[$property['province']])) {
                foreach ($regencies_data_js[$property['province']] as $regency): ?>
                    <option value="<?php echo htmlspecialchars($regency); ?>" <?php echo ($property['regency'] == $regency) ? 'selected' : ''; ?>><?php echo htmlspecialchars($regency); ?></option>
                <?php endforeach;
            }
            ?>
        </select>

        <label for="district_or_area">Kecamatan/Area Spesifik:</label>
        <input type="text" name="district_or_area" placeholder="Kecamatan/Area (misal: Kemang, Menteng)" value="<?php echo htmlspecialchars($property['district_or_area']); ?>">
        <small>Isi dengan kecamatan atau area yang lebih spesifik jika diperlukan.</small>

        <label for="price">Harga (Rp):</label>
        <input type="text" name="price" value="<?php echo htmlspecialchars($formatted_price); ?>" placeholder="Contoh: 100.000.000,00" required>
        <small>Masukkan harga dalam format Rupiah (misal: 100.000.000,00). Maksimum Rp 99.999.999,99.</small>

        <label for="description">Deskripsi Properti:</label>
        <textarea name="description" required><?php echo htmlspecialchars($property['description']); ?></textarea>

        <h3>Pilih Agen Penanggung Jawab:</h3>
        <label for="agent_id">Agen:</label>
        <select name="agent_id" id="agent_id">
            <option value="">-- Pilih Agen (Opsional) --</option>
            <?php foreach ($agents as $agent): ?>
                <option value="<?php echo htmlspecialchars($agent['id']); ?>" <?php echo ($property['agent_id'] == $agent['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($agent['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small>Pilih agen yang akan bertanggung jawab atas properti ini.</small>

        <h3>Detail Spesifikasi</h3>
        <label for="luas_tanah">Luas Tanah (Contoh: 6600 m2):</label>
        <input type="text" name="luas_tanah" value="<?php echo htmlspecialchars($property['luas_tanah']); ?>" placeholder="Luas Tanah">

        <label for="luas_bangunan">Luas Bangunan (Contoh: N/A atau 500 m2):</label>
        <input type="text" name="luas_bangunan" value="<?php echo htmlspecialchars($property['luas_bangunan']); ?>" placeholder="Luas Bangunan">

        <label for="arah_bangunan">Arah Bangunan (Contoh: Timur):</label>
        <input type="text" name="arah_bangunan" value="<?php echo htmlspecialchars($property['arah_bangunan']); ?>" placeholder="Arah Bangunan">

        <label for="jenis_bangunan">Jenis Bangunan (Contoh: N/A atau Residential):</label>
        <input type="text" name="jenis_bangunan" value="<?php echo htmlspecialchars($property['jenis_bangunan']); ?>" placeholder="Jenis Bangunan">

        <label for="jumlah_lantai">Jumlah Lantai (Contoh: 1 atau N/A):</label>
        <input type="text" name="jumlah_lantai" value="<?php echo htmlspecialchars($property['jumlah_lantai']); ?>" placeholder="Jumlah Lantai">

        <label for="kamar_tidur">Kamar Tidur (Contoh: Tidak Ada atau 3):</label>
        <input type="text" name="kamar_tidur" value="<?php echo htmlspecialchars($property['kamar_tidur']); ?>" placeholder="Kamar Tidur">

        <label for="kamar_pembantu">Kamar Pembantu (Contoh: Tidak Ada atau 1):</label>
        <input type="text" name="kamar_pembantu" value="<?php echo htmlspecialchars($property['kamar_pembantu']); ?>" placeholder="Kamar Pembantu">

        <label for="kamar_mandi">Kamar Mandi (Contoh: Tidak Ada atau 2):</label>
        <input type="text" name="kamar_mandi" value="<?php echo htmlspecialchars($property['kamar_mandi']); ?>" placeholder="Kamar Mandi">

        <label for="daya_listrik">Daya Listrik (Contoh: N/A atau 2200 VA):</label>
        <input type="text" name="daya_listrik" value="<?php echo htmlspecialchars($property['daya_listrik']); ?>" placeholder="Daya Listrik">

        <label for="saluran_air">Saluran Air (Contoh: PDAM):</label>
        <input type="text" name="saluran_air" value="<?php echo htmlspecialchars($property['saluran_air']); ?>" placeholder="Saluran Air">

        <label for="jalur_telepon">Jalur Telepon (Contoh: Tidak Ada atau Ya):</label>
        <input type="text" name="jalur_telepon" value="<?php echo htmlspecialchars($property['jalur_telepon']); ?>" placeholder="Jalur Telepon">

        <label for="interior">Interior (Contoh: Kosong atau Full Furnished):</label>
        <input type="text" name="interior" value="<?php echo htmlspecialchars($property['interior']); ?>" placeholder="Interior">

        <label for="garasi_parkir">Garasi/Parkir (Contoh: Tidak Ada atau Carport 2 Mobil):</label>
        <input type="text" name="garasi_parkir" value="<?php echo htmlspecialchars($property['garasi_parkir']); ?>" placeholder="Garasi/Parkir">

        <label for="sertifikat">Sertifikat (Contoh: SHM, HGB):</label>
        <input type="text" name="sertifikat" value="<?php echo htmlspecialchars($property['sertifikat']); ?>" placeholder="Sertifikat">

        <label for="view_count">Jumlah Dilihat (View Count):</label>
        <input type="number" name="view_count" value="<?php echo htmlspecialchars($property['view_count']); ?>" min="0">

        <h3>Gambar Properti Saat Ini:</h3>
        <div class="current-images-grid">
            <?php if (!empty($current_images)): ?>
                <?php foreach ($current_images as $img): ?>
                    <div class="current-image-item">
                        <img src="../Uploads/<?php echo htmlspecialchars($img['image_path']); ?>" alt="Current Image">
                        <a href="?id=<?php echo $property_id; ?>&delete_image_id=<?php echo $img['id']; ?>" class="delete-image-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?');">X</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada gambar yang diunggah untuk properti ini.</p>
            <?php endif; ?>
        </div>

        <h3>Upload Gambar Baru (Tambahkan Maksimal 5 Gambar Lagi):</h3>
        <input type="file" name="images[]" accept="image/*" multiple="multiple">
        <small>Pilih hingga 5 gambar baru untuk ditambahkan ke properti ini.</small>
        <small>Gambar yang sudah ada di atas tidak akan dihapus kecuali Anda mengklik 'X'.</small>

        <button type="submit">Update Properti</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('provinceSelect');
    const regencySelect = document.getElementById('regencySelect');
    const regenciesData = <?php echo json_encode($regencies_data_js); ?>;

    function populateRegencies(selectedProvinceName, currentRegencyName = '') {
        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        regencySelect.disabled = true;

        if (selectedProvinceName && regenciesData[selectedProvinceName]) {
            regenciesData[selectedProvinceName].forEach(regency => {
                const option = document.createElement('option');
                option.value = regency;
                option.textContent = regency;
                if (regency === currentRegencyName) {
                    option.selected = true;
                }
                regencySelect.appendChild(option);
            });
            regencySelect.disabled = false;
        }
    }

    provinceSelect.addEventListener('change', function() {
        populateRegencies(this.value);
    });

    const initialProvince = provinceSelect.value;
    const initialRegency = "<?php echo htmlspecialchars($property['regency'] ?? ''); ?>";
    if (initialProvince) {
        populateRegencies(initialProvince, initialRegency);
    }

    // Format input harga saat pengguna mengetik
    const priceInput = document.querySelector('input[name="price"]');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9,]/g, ''); // Hanya izinkan angka dan koma
        if (value.includes(',')) {
            let parts = value.split(',');
            if (parts[1] && parts[1].length > 2) {
                parts[1] = parts[1].slice(0, 2); // Batasi 2 desimal
            }
            value = parts.join(',');
        }
        e.target.value = value;
    });
});
</script>

<?php include '../includes/footer.php'; ?>