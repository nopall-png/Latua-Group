<?php
// Include dependencies with absolute paths
require __DIR__ . '/../includes/db_connect.php';
require __DIR__ . '/../includes/header.php';

// Validate property ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$property_id = (int)$_GET['id'];

// Fetch property details
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        echo "<div class='container mx-auto p-4'><p class='text-red-600'>Properti tidak ditemukan.</p></div>";
        require __DIR__ . '/../includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching property: " . $e->getMessage());
    echo "<div class='container mx-auto p-4'><p class='text-red-600'>Terjadi kesalahan saat mengambil data properti.</p></div>";
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch agent details if agent_id exists
$agent = null;
if (!empty($property['agent_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, phone_number, email, photo_path FROM agents WHERE id = ?");
        $stmt->execute([$property['agent_id']]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching agent: " . $e->getMessage());
    }
}

// Fetch property images
try {
    $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ? ORDER BY id ASC");
    $stmt->execute([$property_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Error fetching images: " . $e->getMessage());
    $images = [];
}

// Update view count
try {
    $stmt = $pdo->prepare("UPDATE properties SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$property_id]);
} catch (PDOException $e) {
    error_log("Error updating view count: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($property['title'] ?? 'Detail Properti') ?> - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-lato bg-gray-100 text-gray-800">
    <!-- Header Section -->
    <section class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800"><?= htmlspecialchars($property['title'] ?? 'Disewakan Kios Cuan di Apartemen SpringLake, Summarecon Bekasi') ?></h1>
                <p class="text-sm md:text-base text-gray-600"><?= htmlspecialchars($property['district_or_area'] ?? 'Bekasi') ?></p>
                <p class="text-lg md:text-xl text-green-600 font-semibold">Rp <?= number_format($property['price'] ?? 40000000, 0, ',', '.') ?></p>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Image Gallery -->
                <div class="flex-1">
                    <?php if (!empty($images)): ?>
                        <div class="relative w-full h-64 md:h-96 rounded-lg overflow-hidden">
                            <button class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-gray-800 bg-opacity-70 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-opacity-90 z-10 prev-btn">&lt;</button>
                            <img src="../Uploads/properties/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($property['title'] ?? 'Properti') ?>" class="main-image w-full h-full object-cover">
                            <button class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-gray-800 bg-opacity-70 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-opacity-90 z-10 next-btn">&gt;</button>
                        </div>
                        <div class="flex gap-2 mt-2 overflow-x-auto">
                            <?php foreach ($images as $key => $image_path): ?>
                                <img src="../Uploads/properties/<?= htmlspecialchars($image_path) ?>" alt="Thumbnail <?= $key + 1 ?>" class="thumbnail-item w-16 h-16 object-cover rounded border-2 <?= $key === 0 ? 'border-blue-600' : 'border-transparent' ?>" data-index="<?= $key ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-64 md:h-96 rounded-lg overflow-hidden">
                            <img src="../Uploads/properties/default.jpg" alt="No Image" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Specifications -->
                <div class="flex-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Spesifikasi Properti</h2>
                    <table class="w-full text-sm">
                        <tr><th class="w-2/5 text-gray-600 font-medium py-2">ID Properti:</th><td><?= htmlspecialchars($property['id_properti'] ?? '119475') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Tipe Properti:</th><td><?= htmlspecialchars($property['tipe_properti'] ?? 'Apartemen') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Luas Tanah:</th><td><?= htmlspecialchars($property['luas_tanah'] ?? 'N/A') ?> m²</td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Luas Bangunan:</th><td><?= htmlspecialchars($property['luas_bangunan'] ?? '16,87') ?> m²</td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Arah Bangunan:</th><td><?= htmlspecialchars($property['arah_bangunan'] ?? 'N/A') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Jenis Bangunan:</th><td><?= htmlspecialchars($property['jenis_bangunan'] ?? 'N/A') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Lebar Jalan:</th><td><?= htmlspecialchars($property['lebar_jalan'] ?? 'N/A') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Kamar Tidur:</th><td><?= htmlspecialchars($property['kamar_tidur'] ?? 'Tidak Ada') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Kamar Mandi:</th><td><?= htmlspecialchars($property['kamar_mandi'] ?? 'Tidak Ada') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Kamar Pembantu:</th><td><?= htmlspecialchars($property['kamar_pembantu'] ?? 'Tidak Ada') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Sertifikat:</th><td><?= htmlspecialchars($property['sertifikat'] ?? 'SHM') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Jumlah Lantai:</th><td><?= htmlspecialchars($property['jumlah_lantai'] ?? '1') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Daya Listrik:</th><td><?= htmlspecialchars($property['daya_listrik'] ?? '1300 VA') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Saluran Air:</th><td><?= htmlspecialchars($property['saluran_air'] ?? 'PDAM') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Jalur Telepon:</th><td><?= htmlspecialchars($property['jalur_telepon'] ?? 'Tidak Ada') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Jumlah Jalur Telepon:</th><td><?= htmlspecialchars($property['jumlah_jalur_telepon'] ?? 'N/A') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Interior:</th><td><?= htmlspecialchars($property['interior'] ?? 'Kosong') ?></td></tr>
                        <tr><th class="text-gray-600 font-medium py-2">Garasi / Parkir:</th><td><?= htmlspecialchars($property['garasi_parkir'] ?? 'Tidak Ada') ?></td></tr>
                    </table>
                    <div class="mt-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Fasilitas & Fitur Properti</h3>
                        <ul class="list-none p-0">
                            <?php
                            if (!empty($property['facilities'])) {
                                $facilities = explode(',', $property['facilities']);
                                foreach ($facilities as $facility) {
                                    echo '<li class="text-sm text-gray-600">' . htmlspecialchars(trim($facility)) . '</li>';
                                }
                            } else {
                                echo '<li class="text-sm text-gray-600">Tidak ada fasilitas yang tercatat.</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- About Property -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Tentang Properti Ini</h2>
                <p class="text-sm md:text-base text-gray-600"><?= nl2br(htmlspecialchars($property['description'] ?? 'Disewakan Kios Cuan di Apartemen SpringLake, Summarecon Bekasi, Luas kios 16,87 m² (dimensi 2,8 x 6), Listrik 1.300 w, Air PAM, #dvd, Harga 40 juta/tahun, Hub: David AOSB')) ?></p>
                <p class="text-sm text-gray-500 mt-2">Dilihat sebanyak: <?= htmlspecialchars($property['view_count'] ?? '0') ?> Kali</p>
                <div class="flex justify-center gap-4 mt-4">
                    <button class="bg-blue-800 text-white px-4 py-2 rounded-full font-semibold hover:bg-blue-700 transition">Share Properti</button>
                </div>
            </div>

            <!-- Agent Contact -->
            <?php if ($agent): ?>
                <div class="bg-gray-100 p-4 rounded-lg border border-gray-200 text-center md:text-left">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Kontak Agen</h2>
                    <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-gray-700">
                            <?php if ($agent['photo_path'] && file_exists('../Uploads/agents/' . $agent['photo_path'])): ?>
                                <img src="../Uploads/agents/<?= htmlspecialchars($agent['photo_path']) ?>" alt="Foto Agen" class="w-full h-full object-cover">
                            <?php else: ?>
                                <p class="flex items-center justify-center w-full h-full bg-gray-200 text-gray-700 text-sm">No Photo</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-800"><?= htmlspecialchars($agent['name'] ?? 'David AOSB') ?></h3>
                            <p class="text-sm text-gray-600">Telp: <?= htmlspecialchars($agent['phone_number'] ?? '081285724152') ?></p>
                        </div>
                    </div>
                    <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'] ?? '081285724152')) ?>" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 transition inline-flex items-center gap-2">
                        <i class="fab fa-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php require __DIR__ . '/../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mainImage = document.querySelector('.main-image');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            const images = Array.from(thumbnails).map(thumb => thumb.src);
            let currentIndex = 0;

            const updateMainImage = (index) => {
                if (images.length > 0) {
                    mainImage.src = images[index];
                    thumbnails.forEach((thumb, i) => thumb.classList.toggle('border-blue-600', i === index));
                    currentIndex = index;
                }
            };

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', () => updateMainImage(parseInt(thumb.dataset.index)));
            });

            prevBtn?.addEventListener('click', () => {
                if (images.length > 0) {
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                    updateMainImage(currentIndex);
                }
            });

            nextBtn?.addEventListener('click', () => {
                if (images.length > 0) {
                    currentIndex = (currentIndex + 1) % images.length;
                    updateMainImage(currentIndex);
                }
            });

            if (images.length > 0) updateMainImage(0);
        });
    </script>
</body>
</html>