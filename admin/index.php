<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$message = ''; // Pesan untuk hero images
$upload_messages_properties = []; // Pesan untuk properti dan pengajuan
$agent_message = ''; // Pesan untuk agen

// =========================================================
// Logika Pengelolaan GAMBAR HERO (TANPA GD LIBRARY)
// =========================================================

// Proses upload gambar hero baru
if (isset($_POST['upload_hero'])) {
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../Uploads/hero/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $message = "<p class='error-message'>Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF yang diizinkan.</p>";
        } else {
            $new_file_name = uniqid('hero_', true) . '.' . $file_extension;
            $upload_path = $target_dir . $new_file_name;

            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_path)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO hero_images (image_path, is_active) VALUES (?, 0)");
                    $stmt->execute([$new_file_name]);
                    $message = "<p class='success-message'>Gambar hero berhasil diunggah! Sekarang Anda bisa 'Set Aktif' untuk menambahkannya ke slideshow.</p>";
                } catch (PDOException $e) {
                    $message = "<p class='error-message'>Error menyimpan data gambar: " . $e->getMessage() . "</p>";
                }
            } else {
                $message = "<p class='error-message'>Gagal memindahkan file gambar. Pastikan folder 'Uploads/hero/' memiliki izin tulis.</p>";
            }
        }
    } else {
        $message = "<p class='error-message'>Tidak ada gambar yang dipilih atau terjadi error upload: " . $_FILES['hero_image']['error'] . ".</p>";
    }
}

// Proses set/unset gambar hero aktif
if (isset($_GET['action']) && isset($_GET['hero_id']) && is_numeric($_GET['hero_id'])) {
    $hero_id = $_GET['hero_id'];
    $action = $_GET['action'];

    if ($action == 'set_active') {
        try {
            $stmt_count = $pdo->query("SELECT COUNT(*) FROM hero_images WHERE is_active = 1");
            $active_count = $stmt_count->fetchColumn();

            if ($active_count < 3) {
                $stmt_deactivate = $pdo->prepare("UPDATE hero_images SET is_active = 0 WHERE is_active = 1");
                $stmt_deactivate->execute();
                $stmt_activate = $pdo->prepare("UPDATE hero_images SET is_active = 1 WHERE id = ?");
                $stmt_activate->execute([$hero_id]);
                $message = "<p class='success-message'>Gambar hero berhasil diatur sebagai aktif!</p>";
            } else {
                $message = "<p class='error-message'>Maksimal 3 gambar hero aktif diperbolehkan. Harap nonaktifkan satu terlebih dahulu.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error-message'>Error mengatur gambar aktif: " . $e->getMessage() . "</p>";
        }
    } elseif ($action == 'set_inactive') {
        try {
            $stmt_deactivate = $pdo->prepare("UPDATE hero_images SET is_active = 0 WHERE id = ?");
            $stmt_deactivate->execute([$hero_id]);
            $message = "<p class='success-message'>Gambar hero berhasil dinonaktifkan!</p>";
        } catch (PDOException $e) {
            $message = "<p class='error-message'>Error menonaktifkan gambar: " . $e->getMessage() . "</p>";
        }
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?msg=' . urlencode(strip_tags($message)));
    exit();
}

// Proses hapus gambar hero
if (isset($_GET['delete_hero_id']) && is_numeric($_GET['delete_hero_id'])) {
    $delete_id = $_GET['delete_hero_id'];
    $stmt_get_path = $pdo->prepare("SELECT image_path FROM hero_images WHERE id = ?");
    $stmt_get_path->execute([$delete_id]);
    $hero_image_data = $stmt_get_path->fetch();

    if ($hero_image_data) {
        try {
            $stmt_delete = $pdo->prepare("DELETE FROM hero_images WHERE id = ?");
            $stmt_delete->execute([$delete_id]);

            $file_path = '../Uploads/hero/' . $hero_image_data['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
                $message = "<p class='success-message'>Gambar hero berhasil dihapus!</p>";
            } else {
                $message = "<p class='success-message'>Gambar hero berhasil dihapus dari database, tetapi file fisik tidak ditemukan.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error-message'>Error menghapus gambar hero dari database: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p class='error-message'>Gambar hero tidak ditemukan.</p>";
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?msg=' . urlencode(strip_tags($message)));
    exit();
}

// =========================================================
// Logika Pengelolaan AGEN
// =========================================================

// Proses tambah agen baru
if (isset($_POST['add_agent'])) {
    $agent_name = trim($_POST['agent_name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email'] ?? '');

    if (empty($agent_name) || empty($phone_number)) {
        $agent_message = "<p class='error-message'>Nama agen dan nomor telepon harus diisi.</p>";
    } else {
        $agent_photo_path = null;
        if (isset($_FILES['agent_photo']) && $_FILES['agent_photo']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../Uploads/agents/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['agent_photo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            if (in_array($file_extension, $allowed_extensions)) {
                $new_file_name = uniqid('agent_', true) . '.' . $file_extension;
                $upload_path = $target_dir . $new_file_name;

                if (move_uploaded_file($_FILES['agent_photo']['tmp_name'], $upload_path)) {
                    $agent_photo_path = $new_file_name;
                } else {
                    $agent_message = "<p class='error-message'>Gagal mengunggah foto agen.</p>";
                }
            } else {
                $agent_message = "<p class='error-message'>Tipe file foto agen tidak diizinkan. Hanya JPG, JPEG, PNG.</p>";
            }
        }

        if (empty($agent_message)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO agents (name, phone_number, email, photo_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$agent_name, $phone_number, $email, $agent_photo_path]);
                $agent_message = "<p class='success-message'>Agen berhasil ditambahkan!</p>";
            } catch (PDOException $e) {
                $agent_message = "<p class='error-message'>Error menambahkan agen: " . $e->getMessage() . "</p>";
            }
        }
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?agent_msg=' . urlencode(strip_tags($agent_message)));
    exit();
}

// Proses hapus agen
if (isset($_GET['delete_agent_id']) && is_numeric($_GET['delete_agent_id'])) {
    $delete_agent_id = $_GET['delete_agent_id'];
    $stmt_get_agent_path = $pdo->prepare("SELECT photo_path FROM agents WHERE id = ?");
    $stmt_get_agent_path->execute([$delete_agent_id]);
    $agent_photo_data = $stmt_get_agent_path->fetch();

    if ($agent_photo_data) {
        try {
            $stmt_delete_agent = $pdo->prepare("DELETE FROM agents WHERE id = ?");
            $stmt_delete_agent->execute([$delete_agent_id]);

            $file_path = '../Uploads/agents/' . $agent_photo_data['photo_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $agent_message = "<p class='success-message'>Agen berhasil dihapus!</p>";
        } catch (PDOException $e) {
            $agent_message = "<p class='error-message'>Error menghapus agen: " . $e->getMessage() . "</p>";
        }
    } else {
        $agent_message = "<p class='error-message'>Agen tidak ditemukan.</p>";
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?agent_msg=' . urlencode(strip_tags($agent_message)));
    exit();
}

// Ambil data agen
$agents = [];
try {
    $stmt_agents = $pdo->query("SELECT * FROM agents ORDER BY name ASC");
    $agents = $stmt_agents->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agents = [];
    error_log("Error fetching agents: " . $e->getMessage());
}

// Ambil data hero images
$hero_images = [];
try {
    $stmt_hero = $pdo->query("SELECT * FROM hero_images ORDER BY uploaded_at DESC");
    $hero_images = $stmt_hero->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hero_images = [];
    error_log("Error fetching hero images: " . $e->getMessage());
}

// Ambil data properti untuk dijual dengan gambar dari property_images
$properties_for_sale = [];
try {
    $stmt_sale = $pdo->prepare("
        SELECT p.*, pi.image_path AS main_image_path 
        FROM properties p 
        LEFT JOIN property_images pi ON p.id = pi.property_id 
        WHERE p.property_type = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt_sale->execute(['for_sale']);
    $properties_for_sale = $stmt_sale->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties_for_sale = [];
    error_log("Error fetching properties for sale: " . $e->getMessage());
}

// Ambil data properti untuk disewakan dengan gambar dari property_images
$properties_for_rent = [];
try {
    $stmt_rent = $pdo->prepare("
        SELECT p.*, pi.image_path AS main_image_path 
        FROM properties p 
        LEFT JOIN property_images pi ON p.id = pi.property_id 
        WHERE p.property_type = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt_rent->execute(['for_rent']);
    $properties_for_rent = $stmt_rent->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties_for_rent = [];
    error_log("Error fetching properties for rent: " . $e->getMessage());
}

// Ambil data pengajuan properti dari pengguna
$pending_properties = [];
try {
    $stmt_pending = $pdo->query("SELECT * FROM pending_properties ORDER BY created_at DESC");
    $pending_properties = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pending_properties = [];
    error_log("Error fetching pending properties: " . $e->getMessage());
}

// Proses hapus pengajuan properti
if (isset($_GET['delete_pending_id']) && is_numeric($_GET['delete_pending_id'])) {
    $delete_pending_id = $_GET['delete_pending_id'];
    try {
        $stmt_delete_pending = $pdo->prepare("DELETE FROM pending_properties WHERE id = ?");
        $stmt_delete_pending->execute([$delete_pending_id]);
        $upload_messages_properties[] = "<p class='success-message'>Pengajuan properti berhasil dihapus!</p>";
    } catch (PDOException $e) {
        $upload_messages_properties[] = "<p class='error-message'>Error menghapus pengajuan: " . $e->getMessage() . "</p>";
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}
?>

<style>
    /* General Styling */
    .admin-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .admin-section {
        margin-bottom: 40px;
    }
    h2 {
        color: #333;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .upload-hero-form {
        margin-bottom: 20px;
    }
    .upload-hero-form label, .upload-hero-form input, .upload-hero-form button {
        display: block;
        margin-bottom: 10px;
    }
    .btn-upload, .btn-set-active, .btn-delete {
        padding: 5px 10px;
        margin-right: 5px;
        text-decoration: none;
        color: #fff;
        border-radius: 3px;
        cursor: pointer;
    }
    .btn-upload {
        background-color: #28a745;
    }
    .btn-set-active {
        background-color: #007bff;
    }
    .btn-delete {
        background-color: #dc3545;
    }
    .btn-upload:hover, .btn-set-active:hover, .btn-delete:hover {
        opacity: 0.9;
    }
    .success-message {
        color: #28a745;
        background-color: #e9fce9;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .error-message {
        color: #dc3545;
        background-color: #fce9e9;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .active-badge {
        background-color: #28a745;
        color: #fff;
        padding: 2px 8px;
        border-radius: 3px;
        margin-right: 5px;
    }

    /* Hero Images Styling - Updated to Integrate with Images */
    .hero-image-grid {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 15px;
        margin-top: 15px;
        padding-bottom: 10px;
    }
    .hero-image-item {
        flex: 0 0 300px; /* Fixed width for consistency */
        height: auto;
        border: 2px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: relative;
        transition: transform 0.2s;
    }
    .hero-image-item img {
        width: 100%;
        height: 200px; /* Fixed height for uniformity */
        object-fit: cover; /* Ensures image fills the space without distortion */
        display: block;
    }
    .hero-image-actions {
        padding: 10px;
        background-color: #f8f9fa;
        text-align: center;
    }
    .hero-image-actions a {
        display: inline-block;
        margin: 0 5px;
    }

    /* Ensure images integrate with actions */
    .hero-image-item:hover {
        transform: scale(1.02); /* Slight zoom on hover for interactivity */
    }
</style>

<div class="admin-container">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Admin! Manage your properties and site settings below.</p>

    <!-- Section untuk Hero Images -->
    <div class="admin-section">
        <h2>Kelola Gambar Hero Halaman Utama</h2>
        <?php echo $message; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="upload-hero-form">
            <h3>Unggah Gambar Hero Baru:</h3>
            <label for="hero_image">Pilih File (Tidak ada file yang dipilih):</label>
            <input type="file" name="hero_image" id="hero_image" accept="image/jpeg, image/png, image/gif" required>
            <button type="submit" name="upload_hero" class="btn-upload">Unggah Gambar</button>
        </form>

        <h3>Gambar Hero yang Tersedia: (Maksimal 3 Aktif untuk Slideshow)</h3>
        <?php if (empty($hero_images)): ?>
            <p>Belum ada gambar hero yang diunggah.</p>
        <?php else: ?>
            <div class="hero-image-grid">
                <?php foreach ($hero_images as $img): ?>
                    <div class="hero-image-item">
                        <img src="../Uploads/hero/<?php echo htmlspecialchars($img['image_path']); ?>" alt="Hero Image">
                        <div class="hero-image-actions">
                            <?php if ($img['is_active'] == 0): ?>
                                <a href="?action=set_active&hero_id=<?php echo $img['id']; ?>" class="btn-set-active" onclick="return confirm('Atur gambar ini sebagai hero aktif?');">Aktif</a>
                            <?php else: ?>
                                <span class="active-badge">Aktif</span>
                                <a href="?action=set_inactive&hero_id=<?php echo $img['id']; ?>" class="btn-delete" onclick="return confirm('Nonaktifkan gambar ini dari slideshow?');">Nonaktifkan</a>
                            <?php endif; ?>
                            <a href="?delete_hero_id=<?php echo $img['id']; ?>" class="btn-delete" onclick="return confirmDeleteAgent(<?php echo $img['id']; ?>, 'hero');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

    <!-- Section untuk Agen -->
    <div class="admin-section">
        <h2>Kelola Agen</h2>
        <?php echo $agent_message; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="upload-hero-form">
            <h3>Tambah Agen Baru</h3>
            <label for="agent_name">Nama Agen:</label>
            <input type="text" name="agent_name" id="agent_name" required>
            
            <label for="phone_number">Nomor Telepon (untuk WhatsApp):</label>
            <input type="text" name="phone_number" id="phone_number" placeholder="Contoh: 6281234567890 (tanpa +, spasi, atau strip)" required>
            
            <label for="email">Email (Opsional):</label>
            <input type="email" name="email" id="email" placeholder="Contoh: nama@email.com">
            
            <label for="agent_photo">Foto Agen (Opsional):</label>
            <input type="file" name="agent_photo" id="agent_photo" accept="image/jpeg, image/png">
            
            <button type="submit" name="add_agent">Tambah Agen</button>
        </form>

        <h3>Daftar Agen Tersedia:</h3>
        <?php if (empty($agents)): ?>
            <p>Belum ada agen yang ditambahkan.</p>
        <?php else: ?>
            <div class="agent-grid">
                <?php foreach ($agents as $agent): ?>
                    <div class="agent-card-admin">
                        <div class="agent-header">KONTAK AGEN</div>
                        <a href="?delete_agent_id=<?php echo $agent['id']; ?>" class="delete-agent-button" onclick="return confirmDeleteAgent(<?php echo $agent['id']; ?>, 'agent', '<?php echo htmlspecialchars($agent['name']); ?>');">
                            &times;
                        </a>
                        <div class="agent-photo-container">
                            <?php if ($agent['photo_path'] && file_exists('../Uploads/agents/' . $agent['photo_path'])): ?>
                                <img src="../Uploads/agents/<?php echo htmlspecialchars($agent['photo_path']); ?>" alt="Foto Agen">
                            <?php else: ?>
                                <i class="fas fa-user-circle no-photo-icon"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="agent-name"><?php echo htmlspecialchars($agent['name']); ?></h3>
                        <div class="agent-contact-info">
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($agent['phone_number']); ?></p>
                            <?php if (!empty($agent['email'])): ?>
                                <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>"><?php echo htmlspecialchars($agent['email']); ?></a></p>
                            <?php endif; ?>
                            <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'])); ?>" target="_blank" class="btn-whatsapp">
                                <i class="fab fa-whatsapp"></i> Chat WhatsApp
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

    <!-- Section untuk Properti -->
    <div class="admin-section">
        <h2>Kelola Daftar Properti</h2>
        <button class="add-property-btn" onclick="location.href='upload_property.php'">Tambah Properti Baru</button>
        <?php foreach ($upload_messages_properties as $msg): ?>
            <p><?php echo $msg; ?></p>
        <?php endforeach; ?>

        <div class="property-section">
            <h3>Properties for Sale</h3>
            <?php if (empty($properties_for_sale)): ?>
                <p>No properties for sale found.</p>
            <?php else: ?>
                <div class="property-grid">
                    <?php foreach ($properties_for_sale as $property): ?>
                        <div class="property-card">
                            <a href="../detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                                <?php
                                $image_path = !empty($property['main_image_path']) ? "../Uploads/" . htmlspecialchars($property['main_image_path']) : "../Uploads/default.jpg";
                                if (file_exists($image_path)) {
                                    echo "<img src='$image_path' alt='" . htmlspecialchars($property['title']) . "'>";
                                } else {
                                    echo "<img src='../Uploads/default.jpg' alt='" . htmlspecialchars($property['title']) . "'>";
                                }
                                ?>
                            </a>
                            <div class="property-card-content">
                                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                <p><?php echo htmlspecialchars($property['description']); ?></p>
                                <p>Price: $<?php echo number_format($property['price'], 2); ?></p>
                                <p>Type: Sale</p>
                            </div>
                            <div class="card-actions">
                                <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn-edit">Edit</a>
                                <a href="?delete_id=<?php echo $property['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="property-section">
            <h3>Properties for Rent</h3>
            <?php if (empty($properties_for_rent)): ?>
                <p>No properties for rent found.</p>
            <?php else: ?>
                <div class="property-grid">
                    <?php foreach ($properties_for_rent as $property): ?>
                        <div class="property-card">
                            <a href="../detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                                <?php
                                $image_path = !empty($property['main_image_path']) ? "../Uploads/" . htmlspecialchars($property['main_image_path']) : "../Uploads/default.jpg";
                                if (file_exists($image_path)) {
                                    echo "<img src='$image_path' alt='" . htmlspecialchars($property['title']) . "'>";
                                } else {
                                    echo "<img src='../Uploads/default.jpg' alt='" . htmlspecialchars($property['title']) . "'>";
                                }
                                ?>
                            </a>
                            <div class="property-card-content">
                                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                <p><?php echo htmlspecialchars($property['description']); ?></p>
                                <p>Price: $<?php echo number_format($property['price'], 2); ?></p>
                                <p>Type: Rent</p>
                            </div>
                            <div class="card-actions">
                                <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn-edit">Edit</a>
                                <a href="?delete_id=<?php echo $property['id']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus properti ini?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section untuk Pengajuan Properti dari Pengguna -->
    <div class="admin-section">
        <h2>Pengajuan Properti dari Pengguna</h2>
        <?php if (empty($pending_properties)): ?>
            <p>Tidak ada pengajuan properti dari pengguna untuk saat ini.</p>
        <?php else: ?>
            <div class="property-grid">
                <?php foreach ($pending_properties as $pending): ?>
                    <div class="property-card">
                        <div class="property-card-content">
                            <h3>Pengajuan dari: <?php echo htmlspecialchars($pending['user_name']); ?></h3>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($pending['user_email']); ?></p>
                            <p><strong>Telepon:</strong> <?php echo htmlspecialchars($pending['user_phone']); ?></p>
                            <p><strong>Perihal:</strong> <?php echo htmlspecialchars($pending['perihal']); ?></p>
                            <p><strong>Status Properti:</strong> <?php echo htmlspecialchars($pending['status_properti']); ?></p>
                            <p><strong>Detail:</strong> <?php echo htmlspecialchars($pending['detail_properti']); ?></p>
                            <p><strong>Tanggal Pengajuan:</strong> <?php echo date('d/m/Y H:i', strtotime($pending['created_at'])); ?></p>
                        </div>
                        <div class="card-actions">
                            <a href="javascript:void(0);" class="btn-edit" onclick="alert('Silakan salin detail ini dan unggah secara manual melalui opsi \'Tambah Properti Baru\'.');">Terima & Unggah</a>
                            <a href="?delete_pending_id=<?php echo $pending['id']; ?>" class="btn-delete" onclick="return confirmDeleteAgent(<?php echo $pending['id']; ?>, 'pending');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Fungsi JavaScript untuk konfirmasi hapus
function confirmDeleteAgent(id, type, name = '') {
    let confirmMessage;
    let redirectUrl;

    if (type === 'hero') {
        confirmMessage = 'PERHATIAN: Gambar hero ini akan dihapus dari database dan folder. Anda yakin?';
        redirectUrl = '?delete_hero_id=' + id;
    } else if (type === 'agent') {
        confirmMessage = 'Anda yakin ingin menghapus agen ' + name + '?';
        redirectUrl = '?delete_agent_id=' + id;
    } else if (type === 'pending') {
        confirmMessage = 'Apakah Anda yakin ingin menghapus pengajuan ini?';
        redirectUrl = '?delete_pending_id=' + id;
    }

    if (confirm(confirmMessage)) {
        window.location.href = redirectUrl;
    }
}
</script>

<?php include '../includes/footer.php'; ?>