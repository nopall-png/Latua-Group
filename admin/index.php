<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$message = ''; // Pesan untuk hero images
$upload_messages_properties = []; // Pesan untuk properti
$agent_message = ''; // Pesan untuk agen

// =========================================================
// Logika Pengelolaan GAMBAR HERO (TANPA GD LIBRARY)
// =========================================================

// Proses upload gambar hero baru
if (isset($_POST['upload_hero'])) {
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../Uploads/hero/"; // Pastikan folder ini ada dan bisa ditulis
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Buat folder jika belum ada
        }

        $file_extension = strtolower(pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $message = "<p class='error-message'>Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF yang diizinkan.</p>";
        } else {
            // Langsung memindahkan file TANPA pemrosesan gambar (tanpa GD Library)
            $new_file_name = uniqid('hero_', true) . '.' . $file_extension;
            $upload_path = $target_dir . $new_file_name;

            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_path)) {
                try {
                    // Gambar yang baru diupload defaultnya TIDAK aktif, admin harus mengaktifkannya
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
    $action = $_GET['action']; // 'set_active' atau 'set_inactive'

    if ($action == 'set_active') {
        try {
            // Dapatkan jumlah gambar aktif saat ini
            $stmt_count = $pdo->query("SELECT COUNT(*) FROM hero_images WHERE is_active = 1");
            $active_count = $stmt_count->fetchColumn();

            if ($active_count < 3) { // Batasi maksimal 3 gambar aktif untuk slideshow
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
        // Logika menonaktifkan gambar hero
        try {
            $stmt_deactivate = $pdo->prepare("UPDATE hero_images SET is_active = 0 WHERE id = ?");
            $stmt_deactivate->execute([$hero_id]);
            $message = "<p class='success-message'>Gambar hero berhasil dinonaktifkan!</p>";
        } catch (PDOException $e) {
            $message = "<p class='error-message'>Error menonaktifkan gambar: " . $e->getMessage() . "</p>";
        }
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?msg=' . urlencode(strip_tags($message))); // Pass message via URL
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
            // Hapus dari database terlebih dahulu
            $stmt_delete = $pdo->prepare("DELETE FROM hero_images WHERE id = ?");
            $stmt_delete->execute([$delete_id]);

            // Kemudian coba hapus file fisik, tetapi jangan gagal jika file sudah tidak ada
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
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?msg=' . urlencode(strip_tags($message))); // Pass message via URL
    exit();
}

// =========================================================
// Logika Pengelolaan AGEN
// =========================================================

// Proses tambah agen baru
if (isset($_POST['add_agent'])) {
    $agent_name = trim($_POST['agent_name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email'] ?? ''); // Menambahkan input email, default kosong

    if (empty($agent_name) || empty($phone_number)) {
        $agent_message = "<p class='error-message'>Nama agen dan nomor telepon harus diisi.</p>";
    } else {
        $agent_photo_path = null;
        if (isset($_FILES['agent_photo']) && $_FILES['agent_photo']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../Uploads/agents/"; // Folder untuk foto agen
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['agent_photo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png']; // Batasi ekstensi untuk foto agen

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

        if (empty($agent_message)) { // Lanjutkan jika tidak ada error upload foto
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

            // Hapus file foto agen jika ada
            if ($agent_photo_data['photo_path'] && file_exists('../Uploads/agents/' . $agent_photo_data['photo_path'])) {
                unlink('../Uploads/agents/' . $agent_photo_data['photo_path']);
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

// Tangani pesan dari redirect (untuk hero images dan agen)
if (isset($_GET['msg'])) {
    $message = "<p>" . htmlspecialchars($_GET['msg']) . "</p>";
}
if (isset($_GET['agent_msg'])) {
    $agent_message = "<p>" . htmlspecialchars($_GET['agent_msg']) . "</p>";
}

// Ambil semua gambar hero dari database (untuk tampilan di admin)
$stmt_hero_images = $pdo->query("SELECT * FROM hero_images ORDER BY uploaded_at DESC");
$hero_images = $stmt_hero_images->fetchAll();

// Ambil semua agen dari database
$stmt_agents = $pdo->query("SELECT * FROM agents ORDER BY name ASC");
$agents = $stmt_agents->fetchAll();

// =========================================================
// Logika Pengelolaan PROPERTI
// =========================================================

// Process property deletion
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$delete_id]);
        $upload_messages_properties[] = "<p class='success-message'>Property deleted successfully!</p>";
    } catch (PDOException $e) {
        $upload_messages_properties[] = "<p class='error-message'>Error deleting property: " . $e->getMessage() . "</p>";
    }
}

// Fetch properties and their first image
$sql = "
    SELECT
        p.id,
        p.title,
        p.description,
        p.price,
        p.property_type,
        pi.image_path AS main_image_path
    FROM
        properties p
    LEFT JOIN
        (SELECT property_id, MIN(id) AS min_img_id FROM property_images GROUP BY property_id) AS min_images
    ON
        p.id = min_images.property_id
    LEFT JOIN
        property_images pi
    ON
        min_images.min_img_id = pi.id
    ORDER BY
        p.property_type DESC, p.id DESC
";
$stmt = $pdo->query($sql);
$properties = $stmt->fetchAll();

// Group properties by type
$properties_for_sale = [];
$properties_for_rent = [];

foreach ($properties as $property) {
    if ($property['property_type'] == 'for_sale') {
        $properties_for_sale[] = $property;
    } elseif ($property['property_type'] == 'for_rent') {
        $properties_for_rent[] = $property;
    }
}
?>

<div class="admin-container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin! Manage your properties and site settings below.</p>

    <div class="admin-section">
        <h2>Kelola Gambar Hero Halaman Utama</h2>
        <?php echo $message; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="upload-hero-form">
            <label for="hero_image">Unggah Gambar Hero Baru:</label>
            <input type="file" name="hero_image" id="hero_image" accept="image/*" required>
            <button type="submit" name="upload_hero">Unggah Gambar</button>
        </form>

        <h3>Gambar Hero yang Tersedia: (Maksimal 3 Aktif untuk Slideshow)</h3>
        <?php if (empty($hero_images)): ?>
            <p>Belum ada gambar hero yang diunggah.</p>
        <?php else: ?>
            <div class="hero-image-grid">
                <?php foreach ($hero_images as $img): ?>
                    <?php
                        $image_full_path = '../Uploads/hero/' . $img['image_path'];
                        $image_exists = file_exists($image_full_path);
                    ?>
                    <div class="hero-image-item <?php echo $img['is_active'] ? 'active' : ''; ?>">
                        <?php if ($image_exists): ?>
                            <img src="<?php echo htmlspecialchars($image_full_path); ?>" alt="Hero Image">
                        <?php else: ?>
                            <div class="missing-image-placeholder">
                                <i class="fas fa-exclamation-triangle"></i> Gambar Hilang<br>
                                (<?php echo htmlspecialchars($img['image_path']); ?>)
                            </div>
                        <?php endif; ?>
                        <div class="hero-image-actions">
                            <?php if (!$img['is_active']): ?>
                                <a href="?action=set_active&hero_id=<?php echo $img['id']; ?>" class="btn-set-active" onclick="return confirm('Atur gambar ini sebagai hero aktif?');">Set Aktif</a>
                                <a href="?delete_hero_id=<?php echo $img['id']; ?>" class="btn-delete" onclick="return confirm('Hapus gambar ini?');">Hapus</a>
                            <?php else: ?>
                                <span class="active-badge">Aktif</span>
                                <a href="?action=set_inactive&hero_id=<?php echo $img['id']; ?>" class="btn-delete" onclick="return confirm('Nonaktifkan gambar ini dari slideshow?');">Nonaktifkan</a>
                                <a href="?delete_hero_id=<?php echo $img['id']; ?>" class="btn-delete" onclick="return confirm('Hapus gambar ini?');">Hapus</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="admin-section">
        <h2 class="agent-section-title">Kelola Agen</h2>
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
                        <a href="?delete_agent_id=<?php echo $agent['id']; ?>" class="delete-agent-button" onclick="return confirm('Anda yakin ingin menghapus agen <?php echo htmlspecialchars($agent['name']); ?>?');">
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
                            <a href="../detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-image-link">
                                <img src="../Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
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
                                <img src="../Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
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
</div>

<script>
// Fungsi JavaScript untuk konfirmasi hapus, baik untuk hero image atau agen
function confirmDeleteAgent(id, type, name = '') {
    let confirmMessage;
    let redirectUrl;

    if (type === 'hero') {
        confirmMessage = 'PERHATIAN: Gambar hero ini akan dihapus dari database dan folder. Anda yakin?';
        redirectUrl = '?delete_hero_id=' + id;
    } else if (type === 'agent') {
        confirmMessage = 'Anda yakin ingin menghapus agen ' + name + '?';
        redirectUrl = '?delete_agent_id=' + id;
    }

    if (confirm(confirmMessage)) {
        window.location.href = redirectUrl;
    }
}
</script>

<?php include '../includes/footer.php'; ?>