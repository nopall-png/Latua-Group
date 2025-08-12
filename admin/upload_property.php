<?php
ob_start();
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "<span class='error-message'>Silakan login untuk mengunggah properti.</span>";
    header('Location: ../auth/login.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$upload_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_property'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $upload_messages[] = "<span class='error-message'>Token keamanan tidak valid.</span>";
    } else {
        $required_fields = [
            'title' => 'Judul Properti',
            'id_properti' => 'ID Properti',
            'property_type' => 'Tipe Penawaran',
            'tipe_properti' => 'Tipe Properti',
            'province' => 'Provinsi',
            'regency' => 'Kota/Kabupaten',
            'price' => 'Harga',
            'description' => 'Deskripsi'
        ];

        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $upload_messages[] = "<span class='error-message'>$label wajib diisi.</span>";
            }
        }

        if (empty($upload_messages)) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $property_type = $_POST['property_type'];
            $id_properti = trim($_POST['id_properti']);
            $tipe_properti = $_POST['tipe_properti'];
            $luas_tanah = !empty($_POST['luas_tanah']) ? trim($_POST['luas_tanah']) : null;
            $luas_bangunan = !empty($_POST['luas_bangunan']) ? trim($_POST['luas_bangunan']) : null;
            $arah_bangunan = !empty($_POST['arah_bangunan']) ? trim($_POST['arah_bangunan']) : null;
            $jenis_bangunan = !empty($_POST['jenis_bangunan']) ? trim($_POST['jenis_bangunan']) : null;
            $jumlah_lantai = !empty($_POST['jumlah_lantai']) ? trim($_POST['jumlah_lantai']) : null;
            $kamar_tidur = !empty($_POST['kamar_tidur']) ? trim($_POST['kamar_tidur']) : null;
            $kamar_pembantu = !empty($_POST['kamar_pembantu']) ? trim($_POST['kamar_pembantu']) : null;
            $kamar_mandi = !empty($_POST['kamar_mandi']) ? trim($_POST['kamar_mandi']) : null;
            $daya_listrik = !empty($_POST['daya_listrik']) ? trim($_POST['daya_listrik']) : null;
            $saluran_air = !empty($_POST['saluran_air']) ? trim($_POST['saluran_air']) : null;
            $jalur_telepon = !empty($_POST['jalur_telepon']) ? trim($_POST['jalur_telepon']) : null;
            $interior = !empty($_POST['interior']) ? trim($_POST['interior']) : null;
            $garasi_parkir = !empty($_POST['garasi_parkir']) ? trim($_POST['garasi_parkir']) : null;
            $sertifikat = !empty($_POST['sertifikat']) ? trim($_POST['sertifikat']) : null;
            $province = trim($_POST['province']);
            $regency = trim($_POST['regency']);
            $district_or_area = !empty($_POST['district_or_area']) ? trim($_POST['district_or_area']) : null;
            $agent_id = !empty($_POST['agent_id']) && is_numeric($_POST['agent_id']) ? intval($_POST['agent_id']) : null;
            $facilities = !empty($_POST['facilities']) ? trim($_POST['facilities']) : null;

            $uploaded_image_ids_str = $_POST['uploaded_image_ids'] ?? '';
            $uploaded_image_ids = array_filter(explode(',', $uploaded_image_ids_str), 'is_numeric');

            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO properties (
                    id_properti, title, description, price, province, regency, district_or_area,
                    property_type, tipe_properti, luas_tanah, luas_bangunan, arah_bangunan,
                    jenis_bangunan, jumlah_lantai, kamar_tidur, kamar_pembantu, kamar_mandi,
                    daya_listrik, saluran_air, jalur_telepon, interior, garasi_parkir, sertifikat,
                    view_count, agent_id, facilities
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $id_properti, $title, $description, $price, $province, $regency, $district_or_area,
                    $property_type, $tipe_properti, $luas_tanah, $luas_bangunan, $arah_bangunan,
                    $jenis_bangunan, $jumlah_lantai, $kamar_tidur, $kamar_pembantu, $kamar_mandi,
                    $daya_listrik, $saluran_air, $jalur_telepon, $interior, $garasi_parkir, $sertifikat,
                    0, $agent_id, $facilities
                ]);

                $property_id = $pdo->lastInsertId();

                $image_insert_count = 0;
                if (!empty($uploaded_image_ids)) {
                    error_log("Moving images for property_id: $property_id, image_ids: " . implode(',', $uploaded_image_ids));
                    $placeholders = str_repeat('?,', count($uploaded_image_ids) - 1) . '?';
                    $stmt = $pdo->prepare("SELECT id, image_path FROM property_images_temp WHERE id IN ($placeholders)");
                    $stmt->execute($uploaded_image_ids);
                    $temp_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($temp_images) {
                        $stmt_insert = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                        foreach ($temp_images as $image) {
                            $source_path = '../Uploads/' . $image['image_path'];
                            if (file_exists($source_path) && getimagesize($source_path)) {
                                $stmt_insert->execute([$property_id, $image['image_path']]);
                                $image_insert_count++;
                            } else {
                                error_log("Invalid or missing image: $source_path");
                                $upload_messages[] = "<span class='error-message'>Gambar ID {$image['id']} tidak valid atau tidak ditemukan.</span>";
                            }
                        }
                        $stmt_delete = $pdo->prepare("DELETE FROM property_images_temp WHERE id IN ($placeholders)");
                        $stmt_delete->execute($uploaded_image_ids);
                    } else {
                        $upload_messages[] = "<span class='error-message'>Tidak ada gambar di tabel sementara untuk ID: " . implode(',', $uploaded_image_ids) . "</span>";
                    }
                }

                $pdo->commit();
                $upload_messages[] = "<span class='success-message'>Properti berhasil diunggah dengan $image_insert_count gambar!</span>";
                $_SESSION['property_messages'] = $upload_messages;
                header('Location: index.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $upload_messages[] = "<span class='error-message'>Error mengunggah properti: " . $e->getMessage() . "</span>";
            }
        }
    }
    $_SESSION['property_messages'] = $upload_messages;
}

$provinces = $pdo->query("SELECT id, name FROM provinces ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
$regencies_data_js = [];
$stmt = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $regencies_data_js[$row['province_name']][] = $row['regency_name'];
}
$agents = $pdo->query("SELECT id, name FROM agents ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];

$property_types = [
    "Apartemen", "Condotel", "Gedung", "Gudang", "Hotel", "Kantor",
    "Kavling", "Kios", "Komersial", "Kost", "Pabrik", "Ruang Usaha",
    "Ruko", "Rumah", "Rumah Kost", "Tanah"
];

require_once '../includes/header.php';
?>

<style>
    .admin-container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    h2 { color: #333; font-size: 24px; margin-bottom: 15px; }
    h3 { color: #334894; font-size: 18px; margin: 20px 0 10px; }
    form label { display: block; margin: 10px 0 5px; font-weight: 600; }
    form input, form textarea, form select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; }
    form textarea { resize: vertical; min-height: 100px; }
    form button { background-color: #334894; color: #fff; padding: 10px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: 600; }
    form button:hover { background-color: #4a5fb3; }
    .success-message { color: #28a745; background-color: #e9fce9; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    .error-message { color: #dc3545; background-color: #fce9e9; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    .current-images-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .current-image-item { position: relative; width: 100px; height: 100px; }
    .current-image-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
    .current-image-item.loading img { opacity: 0.5; }
    .upload-progress { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; background: rgba(0,0,0,0.5); padding: 2px 5px; border-radius: 3px; }
    .delete-image-btn { position: absolute; top: 5px; right: 5px; color: #dc3545; font-weight: bold; cursor: pointer; }
    small { color: #666; font-size: 0.9em; }
</style>

<div class="admin-container">
    <h2>Upload Properti</h2>
    <?php if (isset($_SESSION['property_messages'])): ?>
        <?php foreach ($_SESSION['property_messages'] as $msg): ?>
            <p><?php echo $msg; ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['property_messages']); ?>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="upload_property" value="1">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <h3>Detail Properti</h3>
        <label for="title">Judul Properti:</label>
        <input type="text" name="title" placeholder="Contoh: Rumah Modern di Jakarta" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
        <label for="id_properti">ID Properti:</label>
        <input type="text" name="id_properti" placeholder="Contoh: 118536" value="<?php echo htmlspecialchars($_POST['id_properti'] ?? ''); ?>" required>
        <label for="property_type">Tipe Penawaran:</label>
        <select name="property_type" required>
            <option value="for_sale" <?php echo ($_POST['property_type'] ?? '') == 'for_sale' ? 'selected' : ''; ?>>Dijual</option>
            <option value="for_rent" <?php echo ($_POST['property_type'] ?? '') == 'for_rent' ? 'selected' : ''; ?>>Disewa</option>
        </select>
        <label for="tipe_properti">Tipe Properti:</label>
        <select name="tipe_properti" required>
            <option value="">Pilih Tipe Properti</option>
            <?php foreach ($property_types as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($_POST['tipe_properti'] ?? '') == $type ? 'selected' : ''; ?>><?php echo htmlspecialchars($type); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="price">Harga (Rp):</label>
        <input type="number" name="price" placeholder="Contoh: 520000000" step="0.01" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
        <label for="description">Deskripsi Properti:</label>
        <textarea name="description" placeholder="Contoh: Dijual Tanah luas 6.600 M di Pulo Gadung" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

        <h3>Spesifikasi Properti</h3>
        <label for="luas_tanah">Luas Tanah (m²):</label>
        <input type="number" name="luas_tanah" placeholder="Contoh: 1628" value="<?php echo htmlspecialchars($_POST['luas_tanah'] ?? ''); ?>">
        <label for="luas_bangunan">Luas Bangunan (m²):</label>
        <input type="number" name="luas_bangunan" placeholder="Contoh: 800" value="<?php echo htmlspecialchars($_POST['luas_bangunan'] ?? ''); ?>">
        <label for="arah_bangunan">Arah Bangunan:</label>
        <input type="text" name="arah_bangunan" placeholder="Contoh: Timur" value="<?php echo htmlspecialchars($_POST['arah_bangunan'] ?? ''); ?>">
        <label for="jenis_bangunan">Jenis Bangunan:</label>
        <input type="text" name="jenis_bangunan" placeholder="Contoh: Residential" value="<?php echo htmlspecialchars($_POST['jenis_bangunan'] ?? ''); ?>">
        <label for="jumlah_lantai">Jumlah Lantai:</label>
        <input type="number" name="jumlah_lantai" placeholder="Contoh: 2" value="<?php echo htmlspecialchars($_POST['jumlah_lantai'] ?? ''); ?>">
        <label for="kamar_tidur">Kamar Tidur:</label>
        <input type="number" name="kamar_tidur" placeholder="Contoh: 8" value="<?php echo htmlspecialchars($_POST['kamar_tidur'] ?? ''); ?>">
        <label for="kamar_pembantu">Kamar Pembantu:</label>
        <input type="number" name="kamar_pembantu" placeholder="Contoh: 1" value="<?php echo htmlspecialchars($_POST['kamar_pembantu'] ?? ''); ?>">
        <label for="kamar_mandi">Kamar Mandi:</label>
        <input type="number" name="kamar_mandi" placeholder="Contoh: 7" value="<?php echo htmlspecialchars($_POST['kamar_mandi'] ?? ''); ?>">
        <label for="daya_listrik">Daya Listrik (VA):</label>
        <input type="number" name="daya_listrik" placeholder="Contoh: 2200" value="<?php echo htmlspecialchars($_POST['daya_listrik'] ?? ''); ?>">
        <label for="saluran_air">Saluran Air:</label>
        <input type="text" name="saluran_air" placeholder="Contoh: PDAM" value="<?php echo htmlspecialchars($_POST['saluran_air'] ?? ''); ?>">
        <label for="jalur_telepon">Jalur Telepon:</label>
        <input type="text" name="jalur_telepon" placeholder="Contoh: Ada" value="<?php echo htmlspecialchars($_POST['jalur_telepon'] ?? ''); ?>">
        <label for="interior">Interior:</label>
        <input type="text" name="interior" placeholder="Contoh: Full Furnished" value="<?php echo htmlspecialchars($_POST['interior'] ?? ''); ?>">
        <label for="garasi_parkir">Garasi/Parkir:</label>
        <input type="text" name="garasi_parkir" placeholder="Contoh: Garasi 2 Mobil" value="<?php echo htmlspecialchars($_POST['garasi_parkir'] ?? ''); ?>">
        <label for="sertifikat">Sertifikat:</label>
        <input type="text" name="sertifikat" placeholder="Contoh: SHM" value="<?php echo htmlspecialchars($_POST['sertifikat'] ?? ''); ?>">

        <h3>Lokasi Properti</h3>
        <label for="province">Provinsi:</label>
        <select name="province" id="provinceSelect" required>
            <option value="">Pilih Provinsi</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo htmlspecialchars($province['name']); ?>" <?php echo ($_POST['province'] ?? '') == $province['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($province['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="regency">Kota/Kabupaten:</label>
        <select name="regency" id="regencySelect" required disabled>
            <option value="">Pilih Kota/Kabupaten</option>
        </select>
        <label for="district_or_area">Kecamatan/Area:</label>
        <input type="text" name="district_or_area" placeholder="Contoh: Kemang, Menteng" value="<?php echo htmlspecialchars($_POST['district_or_area'] ?? ''); ?>">
        <small>Isi dengan kecamatan atau area spesifik jika diperlukan.</small>

        <h3>Pilih Agen</h3>
        <label for="agent_id">Agen:</label>
        <select name="agent_id" id="agent_id">
            <option value="">-- Pilih Agen (Opsional) --</option>
            <?php foreach ($agents as $agent): ?>
                <option value="<?php echo htmlspecialchars($agent['id']); ?>" <?php echo ($_POST['agent_id'] ?? '') == $agent['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($agent['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <small>Pilih agen yang bertanggung jawab atas properti ini.</small>

        <h3>Fasilitas Properti</h3>
        <label for="facilities">Fasilitas (pisahkan dengan koma):</label>
        <textarea name="facilities" placeholder="Contoh: Kolam Renang,Gym,Taman"><?php echo htmlspecialchars($_POST['facilities'] ?? ''); ?></textarea>
        <small>Masukkan fasilitas yang tersedia, dipisahkan dengan koma.</small>

        <h3>Upload Gambar (Maks 5)</h3>
        <input type="file" id="imageUpload" name="images[]" accept="image/*" multiple>
        <small>Pilih hingga 5 gambar. Gambar diunggah langsung setelah dipilih.</small>
        <div id="uploadedImageThumbnails" class="current-images-grid"></div>
        <p id="imageUploadMessage" style="font-size: 0.9em; color: #555;"></p>
        <input type="hidden" name="uploaded_image_ids" id="uploadedImageIds">

        <button type="submit">Upload Properti</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('provinceSelect');
    const regencySelect = document.getElementById('regencySelect');
    const regenciesData = <?php echo json_encode($regencies_data_js); ?>;
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        regencySelect.disabled = true;
        if (selectedProvince && regenciesData[selectedProvince]) {
            regenciesData[selectedProvince].forEach(regency => {
                const option = document.createElement('option');
                option.value = regency;
                option.textContent = regency;
                regencySelect.appendChild(option);
            });
            regencySelect.disabled = false;
        }
    });
    <?php if (!empty($_POST['province']) && isset($regencies_data_js[$_POST['province']])): ?>
        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        <?php foreach ($regencies_data_js[$_POST['province']] as $regency): ?>
            regencySelect.innerHTML += `<option value="<?php echo htmlspecialchars($regency); ?>" <?php echo ($_POST['regency'] ?? '') == $regency ? 'selected' : ''; ?>><?php echo htmlspecialchars($regency); ?></option>`;
        <?php endforeach; ?>
        regencySelect.disabled = false;
    <?php endif; ?>

    const imageUploadInput = document.getElementById('imageUpload');
    const uploadedImageThumbnailsDiv = document.getElementById('uploadedImageThumbnails');
    const imageUploadMessage = document.getElementById('imageUploadMessage');
    const uploadedImageIdsInput = document.getElementById('uploadedImageIds');
    const maxImages = 5;
    let currentImageCount = 0;
    let imageIdMap = {};

    imageUploadInput.addEventListener('change', function(event) {
        const files = event.target.files;
        if (files.length === 0) {
            imageUploadMessage.textContent = 'Tidak ada file yang dipilih.';
            return;
        }

        const filesToUpload = Array.from(files).slice(0, maxImages - currentImageCount);
        if (filesToUpload.length === 0 && currentImageCount >= maxImages) {
            imageUploadMessage.textContent = `Maksimal ${maxImages} gambar.`;
            return;
        } else if (filesToUpload.length < files.length) {
            imageUploadMessage.textContent = `Hanya ${maxImages - currentImageCount} gambar lagi yang dapat diunggah.`;
        } else {
            imageUploadMessage.textContent = `Mengunggah ${filesToUpload.length} gambar...`;
        }

        filesToUpload.forEach(file => {
            if (currentImageCount >= maxImages) return;
            const formData = new FormData();
            formData.append('image', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_image_ajax.php', true);
            const thumbnailItem = document.createElement('div');
            thumbnailItem.className = 'current-image-item loading';
            thumbnailItem.innerHTML = `<img src="" alt="Uploading..." style="opacity: 0.5;"><span class="upload-progress">0%</span>`;
            uploadedImageThumbnailsDiv.appendChild(thumbnailItem);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    thumbnailItem.querySelector('.upload-progress').textContent = `${percent}%`;
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    thumbnailItem.classList.remove('loading');
                    const progressSpan = thumbnailItem.querySelector('.upload-progress');
                    if (progressSpan) progressSpan.remove();

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            thumbnailItem.querySelector('img').src = '../Uploads/' + response.image_path + '?t=' + new Date().getTime();
                            thumbnailItem.querySelector('img').style.opacity = 1;
                            const deleteBtn = document.createElement('a');
                            deleteBtn.className = 'delete-image-btn';
                            deleteBtn.textContent = 'X';
                            deleteBtn.title = 'Hapus gambar';
                            deleteBtn.onclick = function(e) {
                                e.preventDefault();
                                if (confirm('Hapus gambar ini?')) {
                                    fetch('upload_image_ajax.php?action=delete&id=' + response.image_id)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.success) {
                                                thumbnailItem.remove();
                                                currentImageCount--;
                                                delete imageIdMap[response.image_id];
                                                updateHiddenImageIds();
                                                imageUploadMessage.textContent = `Gambar dihapus. Sisa slot: ${maxImages - currentImageCount}.`;
                                            } else {
                                                imageUploadMessage.textContent = 'Gagal menghapus gambar: ' + data.error;
                                            }
                                        })
                                        .catch(err => {
                                            imageUploadMessage.textContent = 'Terjadi kesalahan saat menghapus gambar.';
                                        });
                                }
                            };
                            thumbnailItem.appendChild(deleteBtn);
                            currentImageCount++;
                            imageIdMap[response.image_id] = true;
                            updateHiddenImageIds();
                            imageUploadMessage.textContent = `Berhasil mengunggah ${currentImageCount} gambar. Sisa slot: ${maxImages - currentImageCount}.`;
                        } else {
                            thumbnailItem.remove();
                            imageUploadMessage.textContent = `Gagal mengunggah ${file.name}: ${response.error}`;
                        }
                    } else {
                        thumbnailItem.remove();
                        imageUploadMessage.textContent = `Error server saat mengunggah ${file.name}.`;
                    }
                    imageUploadInput.value = '';
                }
            };
            xhr.send(formData);
        });
    });

    function updateHiddenImageIds() {
        const ids = Object.keys(imageIdMap).filter(id => imageIdMap[id]).join(',');
        uploadedImageIdsInput.value = ids;
    }
});
</script>

<?php
include '../includes/footer.php';
ob_end_flush();
?>