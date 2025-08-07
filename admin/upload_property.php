<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$upload_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Property Details
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $property_type = $_POST['property_type']; // 'for_sale' or 'for_rent'

    // New Specifications
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
    $view_count = 0; // Set default 0 for new uploads
    
    // Lokasi
    $province = $_POST['province'] ?? '';
    $regency = $_POST['regency'] ?? '';
    $district_or_area = $_POST['district_or_area'] ?? '';

    // ID Agen yang dipilih
    $agent_id = !empty($_POST['agent_id']) && is_numeric($_POST['agent_id']) ? $_POST['agent_id'] : null;


    // Ambil daftar ID gambar yang sudah diupload via AJAX dari hidden input
    $uploaded_image_ids_str = $_POST['uploaded_image_ids'] ?? '';
    $uploaded_image_ids = array_filter(explode(',', $uploaded_image_ids_str));

    $target_dir = "../Uploads/"; // Folder utama untuk semua upload

    try {
        // Insert main property details (termasuk kolom lokasi baru dan agent_id)
        $stmt = $pdo->prepare("INSERT INTO properties (
            title, description, price, property_type, view_count,
            id_properti, tipe_properti, luas_tanah, luas_bangunan, arah_bangunan,
            jenis_bangunan, jumlah_lantai, kamar_tidur, kamar_pembantu, kamar_mandi,
            daya_listrik, saluran_air, jalur_telepon, interior, garasi_parkir, sertifikat,
            province, regency, district_or_area, agent_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $title, $description, $price, $property_type, $view_count,
            $id_properti, $tipe_properti, $luas_tanah, $luas_bangunan, $arah_bangunan,
            $jenis_bangunan, $jumlah_lantai, $kamar_tidur, $kamar_pembantu, $kamar_mandi,
            $daya_listrik, $saluran_air, $jalur_telepon, $interior, $garasi_parkir, $sertifikat,
            $province, $regency, $district_or_area, $agent_id
        ]);

        $property_id = $pdo->lastInsertId(); // Get the ID of the newly inserted property

        // Pindahkan gambar dari tabel temporary (property_images_temp) ke tabel utama (property_images)
        // dan hapus dari temporary
        $image_insert_count = 0;
        if (!empty($uploaded_image_ids)) {
            // Gunakan IN clause untuk mengambil semua gambar sekaligus
            $in_placeholders = str_repeat('?,', count($uploaded_image_ids) - 1) . '?';
            $stmt_fetch_temp_images = $pdo->prepare("SELECT image_path FROM property_images_temp WHERE id IN ($in_placeholders)");
            $stmt_fetch_temp_images->execute($uploaded_image_ids);
            $temp_images = $stmt_fetch_temp_images->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($temp_images)) {
                $pdo->beginTransaction(); // Mulai transaksi untuk integritas data
                $stmt_insert_main_image = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                foreach ($temp_images as $image_path_temp) {
                    $stmt_insert_main_image->execute([$property_id, $image_path_temp]);
                    $image_insert_count++;
                }
                // Hapus gambar dari tabel temporary setelah berhasil dipindahkan
                $stmt_delete_temp_images = $pdo->prepare("DELETE FROM property_images_temp WHERE id IN ($in_placeholders)");
                $stmt_delete_temp_images->execute($uploaded_image_ids);
                $pdo->commit(); // Commit transaksi
            }
        }

        if ($image_insert_count > 0) {
            $upload_messages[] = "<span class='success-message'>Properti berhasil diunggah dengan " . $image_insert_count . " gambar!</span>";
        } else {
            $upload_messages[] = "<span style='color: orange;'>Properti berhasil diunggah, tetapi tidak ada gambar yang berhasil dilampirkan.</span>";
        }

    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback jika ada error
        $upload_messages[] = "<span class='error-message'>Error mengunggah properti: " . $e->getMessage() . "</span>";
    }
}

// Daftar Tipe Properti
$property_types = [
    "Apartemen", "Condotel", "Gedung", "Gudang", "Hotel", "Kantor",
    "Kavling", "Kios", "Komersial", "Kost", "Pabrik", "Ruang Usaha",
    "Ruko", "Rumah", "Rumah Kost", "Tanah"
];

// Ambil daftar provinsi dari database
$provinces_stmt = $pdo->query("SELECT id, name FROM provinces ORDER BY name ASC");
$provinces = $provinces_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar kota/kabupaten dari database untuk di-cache di JS
$regencies_data_js = [];
$regencies_stmt = $pdo->query("SELECT p.name AS province_name, r.name AS regency_name FROM regencies r JOIN provinces p ON r.province_id = p.id ORDER BY p.name, r.name ASC");
while($row = $regencies_stmt->fetch(PDO::FETCH_ASSOC)) {
    $regencies_data_js[$row['province_name']][] = $row['regency_name'];
}

// Ambil daftar agen dari database
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
    // regenciesData diambil dari PHP, berisi mapping provinsi ke array kota/kabupaten
    const regenciesData = <?php echo json_encode($regencies_data_js); ?>; 

    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>'; // Reset kota/kabupaten
        regencySelect.disabled = true; // Nonaktifkan sampai ada provinsi terpilih

        if (selectedProvince && regenciesData[selectedProvince]) {
            regenciesData[selectedProvince].forEach(regency => {
                const option = document.createElement('option');
                option.value = regency;
                option.textContent = regency;
                regencySelect.appendChild(option);
            });
            regencySelect.disabled = false; // Aktifkan dropdown kota/kabupaten
        }
    });

    // --- JavaScript untuk AJAX Image Upload ---
    const imageUploadInput = document.getElementById('imageUpload');
    const uploadedImageThumbnailsDiv = document.getElementById('uploadedImageThumbnails');
    const imageUploadMessage = document.getElementById('imageUploadMessage');
    const uploadedImageIdsInput = document.getElementById('uploadedImageIds');
    const maxImages = 5;
    let currentImageCount = 0; // Menghitung jumlah gambar yang sudah berhasil diupload via AJAX
    let imageIdMap = {}; // Untuk menyimpan mapping ID gambar temp dengan elemen thumbnail (contoh: {123: true, 456: true})

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
                return; // Lewati jika sudah mencapai batas saat iterasi
            }

            const formData = new FormData();
            formData.append('image', file); // Nama 'image' harus sesuai dengan $_FILES di upload_image_ajax.php

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_image_ajax.php', true); // Kirim ke skrip AJAX PHP
            
            // Buat thumbnail loading
            const thumbnailItem = document.createElement('div');
            thumbnailItem.className = 'current-image-item loading';
            thumbnailItem.innerHTML = `<img src="" alt="Uploading..." style="opacity: 0.5;">
                                     <span class="upload-progress">0%</span>`;
            uploadedImageThumbnailsDiv.appendChild(thumbnailItem);

            // Event listener untuk progress upload
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    thumbnailItem.querySelector('.upload-progress').textContent = `${percent}%`;
                }
            });

            // Event listener saat request AJAX selesai
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    thumbnailItem.classList.remove('loading');
                    const progressSpan = thumbnailItem.querySelector('.upload-progress');
                    if (progressSpan) progressSpan.remove(); // Hapus indikator progress

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            thumbnailItem.querySelector('img').src = '../Uploads/' + response.image_path;
                            thumbnailItem.querySelector('img').style.opacity = 1;
                            
                            // Tambahkan tombol hapus untuk gambar yang baru diupload
                            const deleteBtn = document.createElement('a');
                            deleteBtn.className = 'delete-image-btn';
                            deleteBtn.textContent = 'X';
                            deleteBtn.title = 'Hapus gambar ini';
                            
                            // Logika penghapusan thumbnail dari DOM dan dari daftar ID
                            deleteBtn.onclick = function(e) {
                                e.preventDefault();
                                if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                                    // Kirim permintaan AJAX ke server untuk menghapus file fisik dan dari tabel temp
                                    fetch('delete_image_ajax.php?id=' + response.image_id, {
                                        method: 'GET' // Atau 'POST' jika Anda kirim data di body
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            thumbnailItem.remove(); // Hapus thumbnail dari DOM
                                            currentImageCount--; // Kurangi hitungan gambar
                                            delete imageIdMap[response.image_id]; // Hapus ID dari map
                                            updateHiddenImageIds(); // Perbarui hidden input
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
                            imageIdMap[response.image_id] = true; // Simpan ID gambar temporer
                            updateHiddenImageIds(); // Perbarui hidden input
                            imageUploadMessage.textContent = `Berhasil mengunggah ${currentImageCount} gambar. Sisa slot: ${maxImages - currentImageCount}.`;
                        } else {
                            thumbnailItem.remove(); // Hapus thumbnail loading jika gagal
                            imageUploadMessage.textContent = `Gagal mengunggah ${file.name}: ${response.error || 'Terjadi kesalahan.'}`;
                        }
                    } else {
                        thumbnailItem.remove(); // Hapus thumbnail loading jika gagal
                        imageUploadMessage.textContent = `Terjadi kesalahan server saat mengunggah ${file.name}. Status: ${xhr.status}`;
                    }
                    // Reset input file agar bisa memilih file yang sama lagi jika perlu
                    imageUploadInput.value = '';
                }
            };
            xhr.send(formData); // Kirim permintaan AJAX
        });
    });

    // Fungsi untuk memperbarui hidden input dengan daftar ID gambar sementara
    function updateHiddenImageIds() {
        const ids = Object.keys(imageIdMap).filter(id => imageIdMap[id]).join(',');
        uploadedImageIdsInput.value = ids;
    }
});
</script>

<?php include '../includes/footer.php'; ?>