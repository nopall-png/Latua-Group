<?php
session_start();
require_once '../includes/db_connect.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
$response = ['success' => false, 'error' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Silakan login untuk mengelola gambar.';
    echo json_encode($response);
    exit();
}

// Handle image upload (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['image']['tmp_name'];
    $original_name = basename($_FILES['image']['name']);
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_name = uniqid() . '.' . $file_extension;
    $upload_dir = '../Uploads/';
    $file_path = $upload_dir . $file_name;

    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $response['error'] = 'Gagal membuat direktori unggahan.';
            echo json_encode($response);
            exit();
        }
    }

    // Validate file type and size
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    if (!in_array($_FILES['image']['type'], $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
        $response['error'] = 'Hanya file JPG, PNG, atau GIF yang diperbolehkan.';
    } elseif ($_FILES['image']['size'] > $max_size) {
        $response['error'] = 'Ukuran file melebihi 5MB.';
    } elseif (!move_uploaded_file($file_tmp, $file_path)) {
        $response['error'] = 'Gagal memindahkan file ke server. Periksa izin direktori.';
    } else {
        // Set permissions
        chmod($file_path, 0644);

        // Verify image integrity
        if (!getimagesize($file_path)) {
            unlink($file_path);
            $response['error'] = 'File gambar rusak atau tidak valid.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO property_images_temp (image_path, mime_type) VALUES (?, ?)");
                $stmt->execute([$file_name, $_FILES['image']['type']]);
                $image_id = $pdo->lastInsertId();
                $response = [
                    'success' => true,
                    'image_id' => $image_id,
                    'image_path' => $file_name,
                    'mime_type' => $_FILES['image']['type']
                ];
            } catch (PDOException $e) {
                unlink($file_path);
                $response['error'] = 'Error menyimpan data gambar: ' . $e->getMessage();
            }
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response['error'] = $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE ? 'Tidak ada file yang diunggah.' : 'Kesalahan upload: ' . $_FILES['image']['error'];
}

// Handle deletion of temporary images (GET with action=delete_temp)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_temp' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $image_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM property_images_temp WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $file_path = '../Uploads/' . $image['image_path'];
            if (file_exists($file_path) && !unlink($file_path)) {
                $response['error'] = 'Gagal menghapus file dari server.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM property_images_temp WHERE id = ?");
                $stmt->execute([$image_id]);
                $response['success'] = true;
            }
        } else {
            $response['error'] = 'Gambar tidak ditemukan.';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Error menghapus gambar: ' . $e->getMessage();
    }
}

// Handle deletion of existing images (GET with action=delete_existing)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_existing' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $image_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $file_path = '../Uploads/' . $image['image_path'];
            if (file_exists($file_path) && !unlink($file_path)) {
                $response['error'] = 'Gagal menghapus file dari server.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM property_images WHERE id = ?");
                $stmt->execute([$image_id]);
                $response['success'] = true;
            }
        } else {
            $response['error'] = 'Gambar tidak ditemukan.';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Error menghapus gambar: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Permintaan tidak valid.';
}

echo json_encode($response);
?>