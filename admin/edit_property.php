<?php
ob_start();
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Silakan login untuk mengedit properti.</span>";
    header('Location: /LatuaGroup/auth/login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['property_message'] = "<span class='text-red-700 bg-red-100 p-2 rounded'>ID properti tidak valid.</span>";
    header('Location: /LatuaGroup/admin/index.php');
    exit();
}

$property_id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$property) {
        $_SESSION['property_message'] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Properti tidak ditemukan.</span>";
        header('Location: /LatuaGroup/admin/index.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['property_message'] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Error mengambil data properti: " . htmlspecialchars($e->getMessage()) . "</span>";
    header('Location: /LatuaGroup/admin/index.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, image_path FROM property_images WHERE property_id = ?");
    $stmt->execute([$property_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    $images = [];
    error_log("Error fetching images for property_id $property_id: " . $e->getMessage());
}

$upload_messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_property'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $upload_messages[] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Token keamanan tidak valid.</span>";
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
                $upload_messages[] = "<span class='text-red-700 bg-red-100 p-2 rounded'>$label wajib diisi.</span>";
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

                $stmt = $pdo->prepare("UPDATE properties SET
                    id_properti = ?, title = ?, description = ?, price = ?, province = ?, regency = ?, district_or_area = ?,
                    property_type = ?, tipe_properti = ?, luas_tanah = ?, luas_bangunan = ?, arah_bangunan = ?,
                    jenis_bangunan = ?, jumlah_lantai = ?, kamar_tidur = ?, kamar_pembantu = ?, kamar_mandi = ?,
                    daya_listrik = ?, saluran_air = ?, jalur_telepon = ?, interior = ?, garasi_parkir = ?, sertifikat = ?,
                    agent_id = ?, facilities = ? WHERE id = ?");
                $stmt->execute([
                    $id_properti, $title, $description, $price, $province, $regency, $district_or_area,
                    $property_type, $tipe_properti, $luas_tanah, $luas_bangunan, $arah_bangunan,
                    $jenis_bangunan, $jumlah_lantai, $kamar_tidur, $kamar_pembantu, $kamar_mandi,
                    $daya_listrik, $saluran_air, $jalur_telepon, $interior, $garasi_parkir, $sertifikat,
                    $agent_id, $facilities, $property_id
                ]);

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
                            $source_path = $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/Uploads/' . $image['image_path'];
                            if (file_exists($source_path) && getimagesize($source_path)) {
                                $stmt_insert->execute([$property_id, $image['image_path']]);
                                $image_insert_count++;
                            } else {
                                error_log("Invalid or missing image: $source_path");
                                $upload_messages[] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Gambar ID {$image['id']} tidak valid atau tidak ditemukan.</span>";
                            }
                        }
                        $stmt_delete = $pdo->prepare("DELETE FROM property_images_temp WHERE id IN ($placeholders)");
                        $stmt_delete->execute($uploaded_image_ids);
                    } else {
                        $upload_messages[] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Tidak ada gambar di tabel sementara untuk ID: " . implode(',', $uploaded_image_ids) . "</span>";
                    }
                }

                $pdo->commit();
                $upload_messages[] = "<span class='text-green-700 bg-green-100 p-2 rounded'>Properti berhasil diperbarui dengan $image_insert_count gambar baru!</span>";
                $_SESSION['property_messages'] = $upload_messages;
                header('Location: /LatuaGroup/admin/index.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $upload_messages[] = "<span class='text-red-700 bg-red-100 p-2 rounded'>Error memperbarui properti: " . htmlspecialchars($e->getMessage()) . "</span>";
            }
        }
    }
    $_SESSION['property_messages'] = $upload_messages;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Properti - Latua Group</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4 max-w-3xl">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Edit Properti</h2>
        <?php if (isset($_SESSION['property_messages'])): ?>
            <div class="mb-4">
                <?php foreach ($_SESSION['property_messages'] as $msg): ?>
                    <p><?php echo $msg; ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['property_messages']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            <input type="hidden" name="edit_property" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="uploaded_image_ids" id="uploadedImageIds">

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Detail Properti</h3>
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Properti</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($property['title']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="id_properti" class="block text-sm font-medium text-gray-700">ID Properti</label>
                <input type="text" name="id_properti" id="id_properti" value="<?php echo htmlspecialchars($property['id_properti']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="property_type" class="block text-sm font-medium text-gray-700">Tipe Penawaran</label>
                <select name="property_type" id="property_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="for_sale" <?php echo $property['property_type'] == 'for_sale' ? 'selected' : ''; ?>>Dijual</option>
                    <option value="for_rent" <?php echo $property['property_type'] == 'for_rent' ? 'selected' : ''; ?>>Disewa</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="tipe_properti" class="block text-sm font-medium text-gray-700">Tipe Properti</label>
                <select name="tipe_properti" id="tipe_properti" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Tipe Properti</option>
                    <?php foreach ($property_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $property['tipe_properti'] == $type ? 'selected' : ''; ?>><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                <input type="number" name="price" id="price" step="0.01" value="<?php echo htmlspecialchars($property['price']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Properti</label>
                <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required><?php echo htmlspecialchars($property['description']); ?></textarea>
            </div>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Spesifikasi Properti</h3>
            <div class="mb-4">
                <label for="luas_tanah" class="block text-sm font-medium text-gray-700">Luas Tanah (m²)</label>
                <input type="number" name="luas_tanah" id="luas_tanah" value="<?php echo htmlspecialchars($property['luas_tanah'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="luas_bangunan" class="block text-sm font-medium text-gray-700">Luas Bangunan (m²)</label>
                <input type="number" name="luas_bangunan" id="luas_bangunan" value="<?php echo htmlspecialchars($property['luas_bangunan'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="arah_bangunan" class="block text-sm font-medium text-gray-700">Arah Bangunan</label>
                <input type="text" name="arah_bangunan" id="arah_bangunan" value="<?php echo htmlspecialchars($property['arah_bangunan'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="jenis_bangunan" class="block text-sm font-medium text-gray-700">Jenis Bangunan</label>
                <input type="text" name="jenis_bangunan" id="jenis_bangunan" value="<?php echo htmlspecialchars($property['jenis_bangunan'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="jumlah_lantai" class="block text-sm font-medium text-gray-700">Jumlah Lantai</label>
                <input type="number" name="jumlah_lantai" id="jumlah_lantai" value="<?php echo htmlspecialchars($property['jumlah_lantai'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="kamar_tidur" class="block text-sm font-medium text-gray-700">Kamar Tidur</label>
                <input type="number" name="kamar_tidur" id="kamar_tidur" value="<?php echo htmlspecialchars($property['kamar_tidur'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="kamar_pembantu" class="block text-sm font-medium text-gray-700">Kamar Pembantu</label>
                <input type="number" name="kamar_pembantu" id="kamar_pembantu" value="<?php echo htmlspecialchars($property['kamar_pembantu'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="kamar_mandi" class="block text-sm font-medium text-gray-700">Kamar Mandi</label>
                <input type="number" name="kamar_mandi" id="kamar_mandi" value="<?php echo htmlspecialchars($property['kamar_mandi'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="daya_listrik" class="block text-sm font-medium text-gray-700">Daya Listrik (VA)</label>
                <input type="number" name="daya_listrik" id="daya_listrik" value="<?php echo htmlspecialchars($property['daya_listrik'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="saluran_air" class="block text-sm font-medium text-gray-700">Saluran Air</label>
                <input type="text" name="saluran_air" id="saluran_air" value="<?php echo htmlspecialchars($property['saluran_air'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="jalur_telepon" class="block text-sm font-medium text-gray-700">Jalur Telepon</label>
                <input type="text" name="jalur_telepon" id="jalur_telepon" value="<?php echo htmlspecialchars($property['jalur_telepon'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="interior" class="block text-sm font-medium text-gray-700">Interior</label>
                <input type="text" name="interior" id="interior" value="<?php echo htmlspecialchars($property['interior'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="garasi_parkir" class="block text-sm font-medium text-gray-700">Garasi/Parkir</label>
                <input type="text" name="garasi_parkir" id="garasi_parkir" value="<?php echo htmlspecialchars($property['garasi_parkir'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="sertifikat" class="block text-sm font-medium text-gray-700">Sertifikat</label>
                <input type="text" name="sertifikat" id="sertifikat" value="<?php echo htmlspecialchars($property['sertifikat'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Lokasi Properti</h3>
            <div class="mb-4">
                <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                <select name="province" id="provinceSelect" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Provinsi</option>
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?php echo htmlspecialchars($province['name']); ?>" <?php echo $property['province'] == $province['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($province['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="regency" class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                <select name="regency" id="regencySelect" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Kota/Kabupaten</option>
                    <?php if (isset($regencies_data_js[$property['province']])): ?>
                        <?php foreach ($regencies_data_js[$property['province']] as $regency): ?>
                            <option value="<?php echo htmlspecialchars($regency); ?>" <?php echo $property['regency'] == $regency ? 'selected' : ''; ?>><?php echo htmlspecialchars($regency); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="district_or_area" class="block text-sm font-medium text-gray-700">Kecamatan/Area</label>
                <input type="text" name="district_or_area" id="district_or_area" value="<?php echo htmlspecialchars($property['district_or_area'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Isi dengan kecamatan atau area spesifik jika diperlukan.</p>
            </div>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Pilih Agen</h3>
            <div class="mb-4">
                <label for="agent_id" class="block text-sm font-medium text-gray-700">Agen</label>
                <select name="agent_id" id="agent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Agen (Opsional) --</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?php echo htmlspecialchars($agent['id']); ?>" <?php echo $property['agent_id'] == $agent['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($agent['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-1">Pilih agen yang bertanggung jawab atas properti ini.</p>
            </div>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Fasilitas Properti</h3>
            <div class="mb-4">
                <label for="facilities" class="block text-sm font-medium text-gray-700">Fasilitas (pisahkan dengan koma)</label>
                <textarea name="facilities" id="facilities" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($property['facilities'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">Masukkan fasilitas yang tersedia, dipisahkan dengan koma.</p>
            </div>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Gambar Saat Ini</h3>
            <?php if (empty($images)): ?>
                <p class="text-gray-500">Tidak ada gambar saat ini.</p>
            <?php else: ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($images as $image): ?>
                        <div class="relative w-24 h-24" data-image-id="<?php echo htmlspecialchars($image['id']); ?>">
                            <img src="/LatuaGroup/Uploads/<?php echo htmlspecialchars($image['image_path']); ?>?t=<?php echo time(); ?>" alt="Property Image" class="w-full h-full object-cover rounded-md">
                            <a class="absolute top-1 right-1 text-red-600 font-bold cursor-pointer hover:text-red-800" title="Hapus gambar" onclick="deleteExistingImage(<?php echo htmlspecialchars($image['id']); ?>, this)">X</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h3 class="text-lg font-semibold mb-2 text-blue-800">Tambah Gambar Baru (Maks 5)</h3>
            <div class="mb-4">
                <input type="file" id="imageUpload" name="images[]" accept="image/*" multiple class="mt-1 block w-full">
                <p class="text-sm text-gray-500 mt-1">Pilih hingga 5 gambar baru. Gambar diunggah langsung setelah dipilih.</p>
                <div id="uploadedImageThumbnails" class="flex flex-wrap gap-2 mt-2"></div>
                <p id="imageUploadMessage" class="text-sm text-gray-500 mt-2"></p>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full font-semibold">Simpan Perubahan</button>
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

        const imageUploadInput = document.getElementById('imageUpload');
        const uploadedImageThumbnailsDiv = document.getElementById('uploadedImageThumbnails');
        const imageUploadMessage = document.getElementById('imageUploadMessage');
        const uploadedImageIdsInput = document.getElementById('uploadedImageIds');
        const maxImages = 5;
        let currentImageCount = <?php echo count($images); ?>;
        let imageIdMap = {};

        imageUploadInput.addEventListener('change', function(event) {
            const files = event.target.files;
            console.log('Selected files:', files); // Debug: Log selected files
            if (files.length === 0) {
                imageUploadMessage.textContent = 'Tidak ada file yang dipilih.';
                console.warn('No files selected in file input');
                return;
            }

            const filesToUpload = Array.from(files).slice(0, maxImages - currentImageCount);
            if (filesToUpload.length === 0 && currentImageCount >= maxImages) {
                imageUploadMessage.textContent = `Maksimal ${maxImages} gambar.`;
                console.warn(`Max images (${maxImages}) reached`);
                return;
            } else if (filesToUpload.length < files.length) {
                imageUploadMessage.textContent = `Hanya ${maxImages - currentImageCount} gambar lagi yang dapat diunggah.`;
                console.log(`Limited to ${maxImages - currentImageCount} files due to max limit`);
            } else {
                imageUploadMessage.textContent = `Mengunggah ${filesToUpload.length} gambar...`;
                console.log(`Uploading ${filesToUpload.length} files`);
            }

            filesToUpload.forEach(file => {
                if (currentImageCount >= maxImages) return;
                const formData = new FormData();
                formData.append('images[]', file);
                formData.append('action', 'upload');
                console.log('Sending FormData for file:', file.name); // Debug: Log file being sent

                fetch('/LatuaGroup/admin/upload_image_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status); // Debug: Log response status
                    return response.json();
                })
                .then(response => {
                    console.log('AJAX response:', response); // Debug: Log server response
                    const thumbnailItem = document.createElement('div');
                    thumbnailItem.className = 'relative w-24 h-24';
                    uploadedImageThumbnailsDiv.appendChild(thumbnailItem);

                    if (response.success) {
                        thumbnailItem.innerHTML = `<img src="/LatuaGroup/Uploads/${response.image_path}?t=${new Date().getTime()}" alt="Uploaded image" class="w-full h-full object-cover rounded-md">`;
                        const deleteBtn = document.createElement('a');
                        deleteBtn.className = 'absolute top-1 right-1 text-red-600 font-bold cursor-pointer hover:text-red-800';
                        deleteBtn.textContent = 'X';
                        deleteBtn.title = 'Hapus gambar';
                        deleteBtn.onclick = function(e) {
                            e.preventDefault();
                            if (confirm('Hapus gambar ini?')) {
                                console.log('Deleting image ID:', response.image_id); // Debug: Log delete action
                                fetch(`/LatuaGroup/admin/upload_image_ajax.php?action=delete_temp&id=${response.image_id}&csrf_token=${encodeURIComponent('<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>')}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        console.log('Delete response:', data); // Debug: Log delete response
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
                                        console.error('Delete error:', err); // Debug: Log fetch error
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
                        console.error('Upload failed:', response.error); // Debug: Log failure
                    }
                    imageUploadInput.value = ''; // Clear input after upload
                })
                .catch(error => {
                    console.error('Fetch error:', error); // Debug: Log fetch error
                    imageUploadMessage.textContent = `Error saat mengunggah ${file.name}: ${error.message}`;
                    const thumbnailItem = document.createElement('div');
                    thumbnailItem.className = 'relative w-24 h-24';
                    uploadedImageThumbnailsDiv.appendChild(thumbnailItem);
                    thumbnailItem.remove();
                });
            });
        });

        function updateHiddenImageIds() {
            const ids = Object.keys(imageIdMap).filter(id => imageIdMap[id]).join(',');
            uploadedImageIdsInput.value = ids;
            console.log('Updated image IDs:', ids); // Debug: Log image IDs
        }

        window.deleteExistingImage = function(imageId, element) {
            if (confirm('Hapus gambar ini?')) {
                fetch(`/LatuaGroup/admin/upload_image_ajax.php?action=delete_existing&id=${imageId}&csrf_token=${encodeURIComponent('<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>')}`)
                    .then(res => res.json())
                    .then(data => {
                        console.log('Delete response:', data); // Debug: Log delete response
                        if (data.success) {
                            element.closest('.relative').remove();
                            currentImageCount--;
                            imageUploadMessage.textContent = `Gambar dihapus. Sisa slot: ${maxImages - currentImageCount}.`;
                        } else {
                            imageUploadMessage.textContent = 'Gagal menghapus gambar: ' + data.error;
                        }
                    })
                    .catch(err => {
                        console.error('Delete error:', err); // Debug: Log fetch error
                        imageUploadMessage.textContent = 'Terjadi kesalahan saat menghapus gambar.';
                    });
            }
        };
    });
    </script>
</body>
</html>

<?php
include $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/footer.php';
ob_end_flush();
?>