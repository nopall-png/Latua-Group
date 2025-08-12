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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
    }

    /* CONTAINER UTAMA */
    .property-detail-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }
    
    .property-detail-card {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    /* HEADER PROPERTI */
    .detail-header {
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    .property-title {
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 5px;
        font-weight: 700;
    }
    
    .property-location {
        font-size: 1rem;
        color: #7f8c8d;
        margin-bottom: 10px;
    }
    
    .price {
        font-size: 1.8rem;
        color: #28a745;
        font-weight: bold;
    }

    /* GALERI GAMBAR */
    .image-gallery {
        margin-bottom: 20px;
    }
    
    .main-image-container {
        position: relative;
        width: 100%;
        height: 400px;
        overflow: hidden;
        border-radius: 8px;
    }
    
    .main-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(44, 62, 80, 0.7);
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
        transition: background-color 0.3s ease;
    }
    
    .prev-btn { left: 10px; }
    .next-btn { right: 10px; }
    
    .gallery-nav-btn:hover { background-color: rgba(44, 62, 80, 0.9); }
    
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        overflow-x: auto;
        padding: 5px 0;
    }
    
    .thumbnail-gallery::-webkit-scrollbar { display: none; }
    
    .thumbnail-item {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 2px solid transparent;
        border-radius: 5px;
        cursor: pointer;
        transition: border-color 0.3s;
    }
    
    .thumbnail-item.active { border-color: #007bff; }
    
    /* SPESIFIKASI PROPERTI */
    .specifications {
        padding: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .specifications h2 {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .specifications table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .specifications th, .specifications td {
        padding: 10px 0;
        text-align: left;
        font-size: 1rem;
    }

    .specifications th {
        width: 40%;
        color: #555;
        font-weight: 500;
        vertical-align: top;
    }
    
    .specifications td {
        color: #333;
        font-weight: 400;
    }
    
    .facilities h3 {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .facilities ul {
        list-style-type: none;
        padding: 0;
    }
    
    .facilities li {
        margin-bottom: 5px;
        color: #555;
    }
    
    /* TENTANG PROPERTI */
    .about-property {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-bottom: 20px;
    }
    
    .about-property h2 {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .about-property p {
        font-size: 1rem;
        color: #555;
        margin-bottom: 15px;
    }
    
    .view-count {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 15px;
    }
    
    /* TOMBOL AKSI */
    .property-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }
    
    .view-button {
        padding: 12px 25px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .share-btn {
        background-color: #007bff;
        color: #fff;
    }
    
    .share-btn:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }
    
    /* KONTAK AGEN */
    .agent-contact-detail-card {
        padding: 20px;
        background: #ecf0f1;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        text-align: center;
    }
    
    .agent-contact-detail-card h2 {
        font-size: 1.5rem;
        color: #34495e;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .agent-info-detail {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .agent-photo-container-detail {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #34495e;
        flex-shrink: 0;
    }
    
    .agent-photo-container-detail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .no-photo-icon-detail {
        font-size: 80px;
        color: #34495e;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    
    .agent-details-text {
        text-align: left;
    }
    
    .agent-name-detail {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .agent-details-text p {
        font-size: 1rem;
        color: #555;
        margin: 0;
    }

    .btn-whatsapp-detail {
        background: linear-gradient(45deg, #10C65A, #0D994B);
        color: white;
        text-decoration: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(16, 198, 90, 0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-top: 15px;
    }
    
    .btn-whatsapp-detail:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(16, 198, 90, 0.6);
    }
    
    /* MEDIA QUERIES */
    /* Mobile-first styling sudah diimplementasikan di atas */
    @media (max-width: 767px) {
        .property-detail-container {
            padding: 10px;
        }

        .property-title {
            font-size: 1.5rem;
        }

        .price {
            font-size: 1.2rem;
        }

        .main-image-container {
            height: 250px;
        }

        .thumbnail-gallery {
            justify-content: center;
        }

        .specifications h2, .about-property h2, .agent-contact-detail-card h2 {
            font-size: 1.3rem;
        }

        .specifications th, .specifications td {
            font-size: 0.9rem;
        }
        
        .agent-info-detail {
            flex-direction: column;
        }
        
        .agent-details-text {
            text-align: center;
        }
    }

    /* Desktop View */
    @media (min-width: 768px) {
        .main-content-desktop {
            display: flex;
            gap: 30px;
        }

        .image-gallery, .specifications {
            flex-basis: 50%;
        }

        .specifications {
            margin-bottom: 0;
            padding: 0;
            border: none;
            border-radius: 0;
        }
        
        .specifications table {
            border-top: 1px solid #e0e0e0;
        }
        
        .specifications th, .specifications td {
            border-bottom: none;
        }
        
        .property-detail-card {
            padding: 40px;
        }
        
        .property-title {
            font-size: 2.5rem;
        }
        
        .price {
            font-size: 2rem;
        }
        
        .main-image-container {
            height: 500px;
        }
        
        .thumbnail-gallery {
            justify-content: flex-start;
        }
        
        .agent-contact-detail-card {
            text-align: left;
            padding: 30px;
        }
        
        .agent-info-detail {
            justify-content: flex-start;
        }
    }
</style>

<div class="property-detail-container">
    <div class="property-detail-card">
        <div class="detail-header">
            <h1 class="property-title"><?php echo htmlspecialchars($property['title'] ?? 'Disewakan Kios Cuan di Apartemen SpringLake, Summarecon Bekasi'); ?></h1>
            <p class="property-location"><?php echo htmlspecialchars($property['district_or_area'] ?? 'bekasi'); ?></p>
            <p class="price">Rp <?php echo number_format($property['price'] ?? 40000000, 0, ',', '.'); ?></p>
        </div>
        
        <div class="main-content-desktop">
            <div class="image-gallery">
                <?php if (!empty($images)): ?>
                    <div class="main-image-container">
                        <button class="gallery-nav-btn prev-btn">&lt;</button>
                        <img src="Uploads/<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($property['title'] ?? 'Disewakan Kios Cuan di Apartemen SpringLake, Summarecon Bekasi'); ?>" class="main-image">
                        <button class="gallery-nav-btn next-btn">&gt;</button>
                    </div>
                    <div class="thumbnail-gallery">
                        <?php foreach ($images as $key => $image_path): ?>
                            <img src="Uploads/<?php echo htmlspecialchars($image_path); ?>" alt="Thumbnail <?php echo $key + 1; ?>" class="thumbnail-item <?php echo ($key == 0) ? 'active' : ''; ?>" data-index="<?php echo $key; ?>">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="main-image-container">
                        <img src="Uploads/default.jpg" alt="No Image" class="main-image">
                    </div>
                <?php endif; ?>
            </div>
        
            <div class="specifications">
                <h2>Spesifikasi Properti</h2>
                <table>
                    <tr><th>ID Properti:</th><td><?php echo htmlspecialchars($property['id_properti'] ?? '119475'); ?></td></tr>
                    <tr><th>Tipe Properti:</th><td><?php echo htmlspecialchars($property['tipe_properti'] ?? 'Apartemen'); ?></td></tr>
                    <tr><th>Luas Tanah:</th><td><?php echo htmlspecialchars($property['luas_tanah'] ?? 'N/A'); ?> m²</td></tr>
                    <tr><th>Luas Bangunan:</th><td><?php echo htmlspecialchars($property['luas_bangunan'] ?? '16,87'); ?> m²</td></tr>
                    <tr><th>Arah Bangunan:</th><td><?php echo htmlspecialchars($property['arah_bangunan'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Jenis Bangunan:</th><td><?php echo htmlspecialchars($property['jenis_bangunan'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Lebar Jalan:</th><td><?php echo htmlspecialchars($property['lebar_jalan'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Kamar Tidur:</th><td><?php echo htmlspecialchars($property['kamar_tidur'] ?? 'Tidak Ada'); ?></td></tr>
                    <tr><th>Kamar Mandi:</th><td><?php echo htmlspecialchars($property['kamar_mandi'] ?? 'Tidak Ada'); ?></td></tr>
                    <tr><th>Kamar Pembantu:</th><td><?php echo htmlspecialchars($property['kamar_pembantu'] ?? 'Tidak Ada'); ?></td></tr>
                    <tr><th>Sertifikat:</th><td><?php echo htmlspecialchars($property['sertifikat'] ?? 'SHM'); ?></td></tr>
                    <tr><th>Jumlah Lantai:</th><td><?php echo htmlspecialchars($property['jumlah_lantai'] ?? '1'); ?></td></tr>
                    <tr><th>Daya Listrik:</th><td><?php echo htmlspecialchars($property['daya_listrik'] ?? '1300 VA'); ?></td></tr>
                    <tr><th>Saluran Air:</th><td><?php echo htmlspecialchars($property['saluran_air'] ?? 'PDAM'); ?></td></tr>
                    <tr><th>Jalur Telepon:</th><td><?php echo htmlspecialchars($property['jalur_telepon'] ?? 'Tidak Ada'); ?></td></tr>
                    <tr><th>Jumlah Jalur Telepon:</th><td><?php echo htmlspecialchars($property['jumlah_jalur_telepon'] ?? 'N/A'); ?></td></tr>
                    <tr><th>Interior:</th><td><?php echo htmlspecialchars($property['interior'] ?? 'Kosong'); ?></td></tr>
                    <tr><th>Garasi / Parkir:</th><td><?php echo htmlspecialchars($property['garasi_parkir'] ?? 'Tidak Ada'); ?></td></tr>
                </table>
                <div class="facilities">
                    <h3>Fasilitas & Fitur Properti</h3>
                    <ul>
                        <?php
                        if (!empty($property['facilities'])) {
                            $facilities = explode(',', $property['facilities']);
                            foreach ($facilities as $facility) {
                                echo '<li>' . htmlspecialchars(trim($facility)) . '</li>';
                            }
                        } else {
                            echo '<li>Tidak ada fasilitas yang tercatat.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="about-property">
            <h2>Tentang Properti Ini</h2>
            <p><?php echo nl2br(htmlspecialchars($property['description'] ?? 'Disewakan Kios Cuan di Apartemen SpringLake, Summarecon Bekasi, Luas kios 16,87 m² (dimensi 2,8 x 6), Listrik 1.300 w, Air PAM, #dvd, Harga 40 juta/tahun, Hub: David AOSB')); ?></p>
            <p class="view-count">Dilihat sebanyak: <?php echo htmlspecialchars($property['view_count'] ?? '0'); ?> Kali</p>
            <div class="property-actions">
                <button class="view-button share-btn">Share Properti Ini</button>
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
                        <h3 class="agent-name-detail"><?php echo htmlspecialchars($agent['name'] ?? 'David AOSB'); ?></h3>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($agent['phone_number'] ?? '081285724152'); ?></p>
                    </div>
                </div>
                <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'] ?? '081285724152')); ?>" target="_blank" class="view-button btn-whatsapp-detail">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
                </a>
            </div>
        <?php endif; ?>
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
        if (images.length > 0) {
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
    }

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            updateMainImage(index);
        });
    });

    prevBtn.addEventListener('click', function() {
        if (images.length > 0) {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateMainImage(currentIndex);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (images.length > 0) {
            currentIndex = (currentIndex + 1) % images.length;
            updateMainImage(currentIndex);
        }
    });

    if (images.length > 0) {
        updateMainImage(0);
    }
});
</script>

<?php include 'includes/footer.php'; ?>