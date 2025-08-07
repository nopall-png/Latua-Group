<?php
include 'includes/db_connect.php';
include 'includes/header.php';

// Pastikan ada ID properti di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php'); // Redirect jika ID tidak valid
    exit();
}

$property_id = $_GET['id'];

// Ambil detail properti dari tabel properties, termasuk agent_id
$stmt_property = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt_property->execute([$property_id]);
$property = $stmt_property->fetch();

if (!$property) {
    echo "<div class='container'><p>Properti tidak ditemukan.</p></div>";
    include 'includes/footer.php';
    exit();
}

// Ambil data agen jika agent_id ada
$agent = null;
if (!empty($property['agent_id'])) {
    $stmt_agent = $pdo->prepare("SELECT id, name, phone_number, email, photo_path FROM agents WHERE id = ?");
    $stmt_agent->execute([$property['agent_id']]);
    $agent = $stmt_agent->fetch(PDO::FETCH_ASSOC);
}

// Ambil semua gambar terkait dari tabel property_images
$stmt_images = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ? ORDER BY id ASC");
$stmt_images->execute([$property_id]);
$images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);

// Update view count
try {
    $stmt_update_views = $pdo->prepare("UPDATE properties SET view_count = view_count + 1 WHERE id = ?");
    $stmt_update_views->execute([$property_id]);
} catch (PDOException $e) {
    error_log("Error updating view count for property ID " . $property_id . ": " . $e->getMessage());
}
?>

<div class="container detail-container">
    <div class="detail-header">
        <h1><?php echo htmlspecialchars($property['title']); ?></h1>
        <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['district_or_area'] ?? '') . ', ' . htmlspecialchars($property['regency']); ?></p>
        <p class="price">Rp. <?php echo number_format($property['price'], 0, ',', '.'); ?></p>
    </div>

    <div class="detail-content">
        <div class="image-gallery">
            <div class="main-image-container">
                <button class="gallery-nav-btn prev-btn">&lt;</button>
                <img src="Uploads/<?php echo htmlspecialchars($images[0] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="main-image">
                <button class="gallery-nav-btn next-btn">&gt;</button>
            </div>
            <div class="thumbnail-gallery">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $key => $image_path): ?>
                        <img src="Uploads/<?php echo htmlspecialchars($image_path); ?>" alt="Thumbnail <?php echo $key + 1; ?>" class="thumbnail-item <?php echo ($key == 0) ? 'active' : ''; ?>" data-index="<?php echo $key; ?>">
                    <?php endforeach; ?>
                <?php else: ?>
                    <img src="Uploads/default.jpg" alt="No Image Thumbnail" class="thumbnail-item active">
                <?php endif; ?>
            </div>
        </div>

        <div class="specifications">
            <h2>Spesifikasi Properti</h2>
            <table>
                <tr><th>ID Properti:</th><td><?php echo htmlspecialchars($property['id_properti'] ?? 'N/A'); ?></td></tr>
                <tr><th>Tipe Properti:</th><td><?php echo htmlspecialchars($property['tipe_properti'] ?? 'N/A'); ?></td></tr>
                <tr><th>Luas Tanah:</th><td><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?></td></tr>
                <tr><th>Luas Bangunan:</th><td><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?></td></tr>
                <tr><th>Arah Bangunan:</th><td><?php echo htmlspecialchars($property['arah_bangunan'] ?? 'N/A'); ?></td></tr>
                <tr><th>Jenis Bangunan:</th><td><?php echo htmlspecialchars($property['jenis_bangunan'] ?? 'N/A'); ?></td></tr>
                <tr><th>Lebar Jalan:</th><td>N/A</td></tr>
                <tr><th>Kamar Tidur:</th><td><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></td></tr>
                <tr><th>Kamar Mandi:</th><td><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></td></tr>
                <tr><th>* Security 24 Jam</th><td></td></tr>
            </table>
        </div>
    </div>

    <div class="about-property">
        <h2>Tentang Properti Ini</h2>
        <p><?php echo nl2br(htmlspecialchars($property['description'] ?? 'Tidak ada deskripsi.')); ?></p>
        <p class="view-count">Dilihat sebanyak : <?php echo htmlspecialchars($property['view_count'] ?? '0'); ?> Kali</p>
        <div class="property-actions">
            <button class="share-btn">Share Properti Ini</button>
            <button class="delete-btn">Hapus abct xyz</button>
        </div>
    </div>

    <?php if ($agent): ?>
    <div class="agent-contact-detail-card">
        <h2>Kontak Agen</h2>
        <div class="agent-info-detail">
            <div class="agent-photo-container-detail">
                <?php if ($agent['photo_path'] && file_exists('Uploads/agents/' . $agent['photo_path'])): ?>
                    <img src="Uploads/agents/<?php echo htmlspecialchars($agent['photo_path']); ?>" alt="Foto Agen">
                <?php else: ?>
                    <i class="fas fa-user-circle no-photo-icon-detail"></i>
                <?php endif; ?>
            </div>
            <div class="agent-details-text">
                <h3 class="agent-name-detail"><?php echo htmlspecialchars($agent['name']); ?></h3>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($agent['phone_number']); ?></p>
                <?php if (!empty($agent['email'])): ?>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>"><?php echo htmlspecialchars($agent['email']); ?></a></p>
                <?php endif; ?>
            </div>
        </div>
        <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'])); ?>" target="_blank" class="btn-whatsapp-detail">
            <i class="fab fa-whatsapp"></i> Chat WhatsApp
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.querySelector('.main-image');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let currentIndex = 0;
        const images = Array.from(thumbnails).map(thumb => thumb.src);

        function updateMainImage(index) {
            mainImage.src = images[index];
            thumbnails.forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
            currentIndex = index;
        }

        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                updateMainImage(index);
            });
        });

        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateMainImage(currentIndex);
        });

        nextBtn.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % images.length;
            updateMainImage(currentIndex);
        });

        if (images.length > 0) {
            updateMainImage(0);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>