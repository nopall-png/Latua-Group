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

<div class="full-width-container">
    <div class="property-detail-container">
        <div class="property-detail-card">
            <div class="detail-header">
                <h1 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h1>
                <p class="property-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['district_or_area'] ?? '') . ', ' . htmlspecialchars($property['regency'] ?? ''); ?></p>
                <p class="price">Rp <?php echo number_format($property['price'] ?? 0, 0, ',', '.'); ?></p>
            </div>

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
                    <tr><th>Luas Tanah:</th><td><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</td></tr>
                    <tr><th>Luas Bangunan:</th><td><?php echo htmlspecialchars($property['luas_bangunan'] ?? 'N/A'); ?> m²</td></tr>
                    <tr><th>Arah Bangunan:</th><td><?php echo htmlspecialchars($property['arah_bangunan'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Jenis Bangunan:</th><td><?php echo htmlspecialchars($property['jenis_bangunan'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Lebar Jalan:</th><td>N/A</td></tr>
                    <tr><th>Kamar Tidur:</th><td><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Kamar Mandi:</th><td><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'N/A'); ?></td></tr>
                    <tr><th>* Security 24 Jam</th><td></td></tr>
                </table>
            </div>

            <div class="about-property">
                <h2>Tentang Properti Ini</h2>
                <p><?php echo nl2br(htmlspecialchars($property['description'] ?? 'Tidak ada deskripsi.')); ?></p>
                <p class="view-count">Dilihat sebanyak: <?php echo htmlspecialchars($property['view_count'] ?? '0'); ?> Kali</p>
                <div class="property-actions">
                    <button class="view-button share-btn">Share Properti Ini</button>
                    <button class="view-button delete-btn">Hapus abct xyz</button>
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
                    <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'])); ?>" target="_blank" class="view-button btn-whatsapp-detail">
                        <i class="fab fa-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
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

<style>
    /* ... (Kode CSS sebelumnya tetap ada) ... */

/* Detail Property Styles */
.property-detail-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
}

.property-detail-card {
    background: #FFFFFF;
    border: 2px solid #334894;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(51, 72, 148, 0.2);
    margin-bottom: 20px;
}

.detail-header {
    padding: 20px;
    text-align: center;
    background-color: #f8f9fa;
}

.property-title {
    font-size: 2rem;
    color: #334894;
    margin-bottom: 10px;
    font-weight: 400;
    font-family: 'Lato', sans-serif;
}

.property-location {
    font-size: 1.2rem;
    color: #333;
    margin-bottom: 10px;
}

.price {
    font-size: 1.5rem;
    color: #28a745;
    font-weight: bold;
    margin-bottom: 10px;
}

.image-gallery {
    position: relative;
    margin-bottom: 20px;
}

.main-image-container {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
}

.main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-bottom: 2px solid #334894;
}

.gallery-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: #334894;
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 10;
    font-size: 1.2rem;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.prev-btn {
    left: 10px;
}

.next-btn {
    right: 10px;
}

.gallery-nav-btn:hover {
    background-color: #4a5fb3;
}

.thumbnail-gallery {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    overflow-x: auto;
    padding: 5px 0;
}

.thumbnail-item {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border: 2px solid #ddd;
    cursor: pointer;
    transition: border-color 0.3s;
}

.thumbnail-item.active {
    border-color: #334894;
}

.specifications {
    padding: 20px;
}

.specifications h2 {
    font-size: 1.5rem;
    color: #334894;
    margin-bottom: 15px;
    font-family: 'Lato', sans-serif;
}

.specifications table {
    width: 100%;
    border-collapse: collapse;
}

.specifications th, .specifications td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.specifications th {
    width: 30%;
    color: #334894;
    font-weight: 600;
}

.specifications td {
    color: #333;
}

.about-property {
    padding: 20px;
    background-color: #f8f9fa;
}

.about-property h2 {
    font-size: 1.5rem;
    color: #334894;
    margin-bottom: 15px;
    font-family: 'Lato', sans-serif;
}

.about-property p {
    font-size: 1rem;
    color: #333;
    margin-bottom: 15px;
}

.view-count {
    font-size: 1rem;
    color: #666;
    margin-bottom: 15px;
}

.property-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.share-btn, .delete-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
}

.share-btn {
    background-color: #007bff;
    color: #fff;
}

.delete-btn {
    background-color: #dc3545;
    color: #fff;
}

.share-btn:hover, .delete-btn:hover {
    opacity: 0.9;
}

.agent-contact-detail-card {
    padding: 20px;
    background: #FFFFFF;
    border: 2px solid #334894;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(51, 72, 148, 0.2);
    margin-top: 20px;
}

.agent-contact-detail-card h2 {
    font-size: 1.5rem;
    color: #334894;
    margin-bottom: 15px;
    text-align: center;
    font-family: 'Lato', sans-serif;
}

.agent-info-detail {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 15px;
}

.agent-photo-container-detail {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #334894;
}

.agent-photo-container-detail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-photo-icon-detail {
    font-size: 100px;
    color: #334894;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.agent-details-text {
    flex-grow: 1;
}

.agent-name-detail {
    font-size: 1.3rem;
    color: #334894;
    margin-bottom: 10px;
    font-family: 'Lato', sans-serif;
}

.agent-details-text p {
    font-size: 1rem;
    color: #333;
    margin-bottom: 5px;
}

.agent-details-text a {
    color: #007bff;
    text-decoration: none;
}

.agent-details-text a:hover {
    text-decoration: underline;
}

.btn-whatsapp-detail {
    display: inline-block;
    background-color: #4CAF50;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    text-align: center;
}

.btn-whatsapp-detail:hover {
    background-color: #45A049;
}

.btn-whatsapp-detail i {
    margin-right: 5px;
}

/* Responsivitas yang Ditingkatkan */
@media (max-width: 768px) {
    .main-image-container {
        height: 250px;
    }

    .thumbnail-item {
        width: 60px;
        height: 60px;
    }

    .property-title {
        font-size: 1.5rem;
    }

    .price {
        font-size: 1.2rem;
    }

    .agent-info-detail {
        flex-direction: column;
        text-align: center;
    }

    .agent-photo-container-detail {
        margin: 0 auto 15px;
    }

    .specifications th, .specifications td {
        font-size: 0.9rem;
        padding: 8px;
    }

    .gallery-nav-btn {
        width: 30px;
        height: 30px;
        font-size: 1rem;
    }

    .property-actions {
        flex-direction: column;
    }

    .share-btn, .delete-btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

@media (max-width: 480px) {
    .property-detail-card {
        padding: 10px;
    }

    .property-title {
        font-size: 1.2rem;
    }

    .price {
        font-size: 1rem;
    }

    .specifications h2 {
        font-size: 1.2rem;
    }

    .thumbnail-gallery {
        justify-content: center;
    }
}
</style>