<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$upload_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input wajib
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
            return;
        }
    }

    // Basic Property Details
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $property_type = $_POST['property_type'];

    // New Specifications
    $id_properti = $_POST['id_properti'];
    $tipe_properti = $_POST['tipe_properti'];
    $luas_tanah = $_POST['luas_tanah'] ?? null;
    $luas_bangunan = $_POST['luas_bangunan'] ?? null;
    $arah_bangunan = $_POST['arah_bangunan'] ?? null;
    $jenis_bangunan = $_POST['jenis_bangunan'] ?? null;
    $jumlah_lantai = $_POST['jumlah_lantai'] ?? null;
    $kamar_tidur = $_POST['kamar_tidur'] ?? null;
    $kamar_pembantu = $_POST['kamar_pembantu'] ?? null;
    $kamar_mandi = $_POST['kamar_mandi'] ?? null;
    $daya_listrik = $_POST['daya_listrik'] ?? null;
    $saluran_air = $_POST['saluran_air'] ?? null;
    $jalur_telepon = $_POST['jalur_telepon'] ?? null;
    $interior = $_POST['interior'] ?? null;
    $garasi_parkir = $_POST['garasi_parkir'] ?? null;
    $sertifikat = $_POST['sertifikat'] ?? null;
    $view_count = 0;

    // Lokasi
    $province = $_POST['province'];
    $regency = $_POST['regency'];
    $district_or_area = $_POST['district_or_area'] ?? '';

    // ID Agen
    $agent_id = !empty($_POST['agent_id']) && is_numeric($_POST['agent_id']) ? $_POST['agent_id'] : null;

    // Ambil daftar ID gambar
    $uploaded_image_ids_str = $_POST['uploaded_image_ids'] ?? '';
    $uploaded_image_ids = array_filter(explode(',', $uploaded_image_ids_str));

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Insert main property details
        $stmt = $pdo->prepare("INSERT INTO properties (
            title, description, price, property_type, view_count,
            id_properti, tipe_properti, luas_tanah, luas_bangunan, arah_bangunan,
            jenis_bangunan, jumlah_lantai, kamar_tidur, kamar_pembantu, kamar_mandi,
            daya_listrik, saluran_air, jalur_telepon, interior, garasi_parkir, sertifikat,
            province, regency, district_or_area, agent_id, image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $title, $description, $price, $property_type, $view_count,
            $id_properti, $tipe_properti, $luas_tanah, $luas_bangunan, $arah_bangunan,
            $jenis_bangunan, $jumlah_lantai, $kamar_tidur, $kamar_pembantu, $kamar_mandi,
            $daya_listrik, $saluran_air, $jalur_telepon, $interior, $garasi_parkir, $sertifikat,
            $province, $regency, $district_or_area, $agent_id, null
        ]);

        $property_id = $pdo->lastInsertId();

        // Pindahkan gambar dari tabel temporary
        $image_insert_count = 0;
        if (!empty($uploaded_image_ids)) {
            $in_placeholders = str_repeat('?,', count($uploaded_image_ids) - 1) . '?';
            $stmt_fetch_temp_images = $pdo->prepare("SELECT id, image_path FROM property_images_temp WHERE id IN ($in_placeholders)");
            $stmt_fetch_temp_images->execute($uploaded_image_ids);
            $temp_images = $stmt_fetch_temp_images->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($temp_images)) {
                $stmt_insert_main_image = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                foreach ($temp_images as $image) {
                    $stmt_insert_main_image->execute([$property_id, $image['image_path']]);
                    $image_insert_count++;
                }
                $stmt_delete_temp_images = $pdo->prepare("DELETE FROM property_images_temp WHERE id IN ($in_placeholders)");
                $stmt_delete_temp_images->execute($uploaded_image_ids);
            } else {
                $upload_messages[] = "<span style='color: orange;'>Tidak ada gambar ditemukan di tabel sementara untuk ID: " . implode(',', $uploaded_image_ids) . "</span>";
            }
        } else {
            $upload_messages[] = "<span style='color: orange;'>Tidak ada ID gambar yang dikirim untuk diproses.</span>";
        }

        $pdo->commit();

        if ($image_insert_count > 0) {
            $upload_messages[] = "<span class='success-message'>Properti berhasil diunggah dengan " . $image_insert_count . " gambar!</span>";
        } else {
            $upload_messages[] = "<span style='color: orange;'>Properti berhasil diunggah, tetapi tidak ada gambar yang berhasil dilampirkan.</span>";
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        $upload_messages[] = "<span class='error-message'>Error mengunggah properti: " . $e->getMessage() . " (Code: " . $e->getCode() . ")</span>";
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
?>

<div class="admin-container">
    <h2>Upload Property</h2>

    <?php foreach ($upload_messages as $msg): ?>
        <p><?php echo $msg; ?></p>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Judul Properti:</label>
        <input type="text" name="title" placeholder="Judul Properti (Contoh: Rumah Modern di Jakarta)" required>

        <label for="id_properti">ID Properti (Contoh: 118536):</label>
        <input type="text" name="id_properti" placeholder="ID Properti Unik" required>

        <label for="property_type">Tipe Penawaran:</label>
        <select name="property_type" required>
            <option value="for_sale">For Sale</option>
            <option value="for_rent">For Rent</option>
        </select>

        <label for="tipe_properti">Tipe Properti:</label>
        <select name="tipe_properti" required>
            <option value="">Pilih Tipe Properti</option>
            <?php foreach ($property_types as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
            <?php endforeach; ?>
        </select>
        
        <h3>Spesifikasi Properti:</h3>
        <label for="luas_tanah">Luas Tanah (m2):</label>
        <input type="number" name="luas_tanah" placeholder="Contoh: 1628">
        
        <label for="luas_bangunan">Luas Bangunan (m2):</label>
        <input type="number" name="luas_bangunan" placeholder="Contoh: 800">
        
        <label for="arah_bangunan">Arah Bangunan:</label>
        <input type="text" name="arah_bangunan" placeholder="Contoh: Timur">
        
        <label for="jenis_bangunan">Jenis Bangunan:</label>
        <input type="text" name="jenis_bangunan" placeholder="Contoh: Residential">
        
        <label for="jumlah_lantai">Jumlah Lantai:</label>
        <input type="number" name="jumlah_lantai" placeholder="Contoh: 2">
        
        <label for="kamar_tidur">Kamar Tidur:</label>
        <input type="number" name="kamar_tidur" placeholder="Contoh: 8">
        
        <label for="kamar_pembantu">Kamar Pembantu:</label>
        <input type="number" name="kamar_pembantu" placeholder="Contoh: 1">
        
        <label for="kamar_mandi">Kamar Mandi:</label>
        <input type="number" name="kamar_mandi" placeholder="Contoh: 7">
        
        <label for="daya_listrik">Daya Listrik (VA):</label>
        <input type="number" name="daya_listrik" placeholder="Contoh: 2200">
        
        <label for="saluran_air">Saluran Air:</label>
        <input type="text" name="saluran_air" placeholder="Contoh: PDAM">
        
        <label for="jalur_telepon">Jalur Telepon:</label>
        <input type="text" name="jalur_telepon" placeholder="Contoh: Ada">
        
        <label for="interior">Interior:</label>
        <input type="text" name="interior" placeholder="Contoh: Full Furnished">
        
        <label for="garasi_parkir">Garasi/Parkir:</label>
        <input type="text" name="garasi_parkir" placeholder="Contoh: Garasi 2 Mobil">
        
        <label for="sertifikat">Sertifikat:</label>
        <input type="text" name="sertifikat" placeholder="Contoh: SHM">

        <h3>Lokasi Properti:</h3>
        <label for="province">Provinsi:</label>
        <select name="province" id="provinceSelect" required>
            <option value="">Pilih Provinsi</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo htmlspecialchars($province['name']); ?>"><?php echo htmlspecialchars($province['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="regency">Kota/Kabupaten:</label>
        <select name="regency" id="regencySelect" required disabled>
            <option value="">Pilih Kota/Kabupaten</option>
        </select>

        <label for="district_or_area">Kecamatan/Area Spesifik:</label>
        <input type="text" name="district_or_area" placeholder="Kecamatan/Area (misal: Kemang, Menteng)">
        <small>Isi dengan kecamatan atau area yang lebih spesifik jika diperlukan.</small>

        <label for="price">Harga:</label>
        <input type="number" name="price" placeholder="Harga (misal: 520000000)" step="0.01" required>

        <label for="description">Deskripsi Properti:</label>
        <textarea name="description" placeholder="Deskripsi Properti (misal: Dijual Tanah luas 6.600 M di Pulo gadung Jakarta Timur)" required></textarea>

        <h3>Pilih Agen Penanggung Jawab:</h3>
        <label for="agent_id">Agen:</label>
        <select name="agent_id" id="agent_id">
            <option value="">-- Pilih Agen (Opsional) --</option>
            <?php foreach ($agents as $agent): ?>
                <option value="<?php echo htmlspecialchars($agent['id']); ?>"><?php echo htmlspecialchars($agent['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <small>Pilih agen yang akan bertanggung jawab atas properti ini.</small>

        <h3>Upload Gambar (Maksimal 5)</h3>
        <input type="file" id="imageUpload" name="images[]" accept="image/*" multiple="multiple">
        <small>Pilih hingga 5 gambar untuk properti ini. Gambar akan diunggah segera setelah dipilih.</small>

        <div id="uploadedImageThumbnails" class="current-images-grid">
        </div>
        <p id="imageUploadMessage" style="font-size: 0.9em; color: #555;"></p>

        <input type="hidden" name="uploaded_image_ids" id="uploadedImageIds">

        <button type="submit">Upload Properti</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- JavaScript untuk Cascading Dropdown Lokasi ---
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

    // --- JavaScript untuk AJAX Image Upload ---
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
            imageUploadMessage.textContent = `Anda sudah mengunggah ${maxImages} gambar. Hapus gambar yang ada untuk mengunggah lebih banyak.`;
            return;
        } else if (filesToUpload.length < files.length) {
            imageUploadMessage.textContent = `Hanya dapat mengunggah ${maxImages - currentImageCount} gambar lagi. Beberapa file telah dilewati.`;
        } else {
            imageUploadMessage.textContent = `Mengunggah ${filesToUpload.length} gambar...`;
        }

        filesToUpload.forEach(file => {
            if (currentImageCount >= maxImages) {
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_image_ajax.php', true);

            const thumbnailItem = document.createElement('div');
            thumbnailItem.className = 'current-image-item loading';
            thumbnailItem.innerHTML = `<img src="" alt="Uploading..." style="opacity: 0.5;">
                                      <span class="upload-progress">0%</span>`;
            uploadedImageThumbnailsDiv.appendChild(thumbnailItem);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    thumbnailItem.querySelector('.upload-progress').textContent = `${percent}%`;
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    thumbnailItem.classList.remove('loading');
                    const progressSpan = thumbnailItem.querySelector('.upload-progress');
                    if (progressSpan) progressSpan.remove();

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            thumbnailItem.querySelector('img').src = '../Uploads/' + response.image_path;
                            thumbnailItem.querySelector('img').style.opacity = 1;

                            const deleteBtn = document.createElement('a');
                            deleteBtn.className = 'delete-image-btn';
                            deleteBtn.textContent = 'X';
                            deleteBtn.title = 'Hapus gambar ini';

                            deleteBtn.onclick = function(e) {
                                e.preventDefault();
                                if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                                    fetch('delete_image_ajax.php?id=' + response.image_id, {
                                        method: 'GET'
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            thumbnailItem.remove();
                                            currentImageCount--;
                                            delete imageIdMap[response.image_id];
                                            updateHiddenImageIds();
                                            imageUploadMessage.textContent = `Gambar ${response.image_path} dihapus. Sisa slot: ${maxImages - currentImageCount}.`;
                                        } else {
                                            alert('Gagal menghapus gambar: ' + data.error);
                                        }
                                    })
                                    .catch(err => {
                                        console.error('Error deleting image via AJAX:', err);
                                        alert('Terjadi kesalahan saat menghapus gambar.');
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
                            imageUploadMessage.textContent = `Gagal mengunggah ${file.name}: ${response.error || 'Terjadi kesalahan.'}`;
                        }
                    } else {
                        thumbnailItem.remove();
                        imageUploadMessage.textContent = `Terjadi kesalahan server saat mengunggah ${file.name}. Status: ${xhr.status}`;
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

<?php include '../includes/footer.php'; ?>