<?php
session_start();

// Define base path
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup';

// Include database connection
$dbPath = $basePath . '/includes/db_connect.php';
if (!file_exists($dbPath)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Could not find db_connect.php at ' . htmlspecialchars($dbPath)]);
    exit();
}
require_once $dbPath;

ini_set('display_errors', 1); // Enable for debugging (set to 0 in production)
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Akses ditolak. Silakan login.']);
    exit();
}

// Set JSON header
header('Content-Type: application/json');

// Get action from URL or POST, default to 'upload'
$action = $_GET['action'] ?? $_POST['action'] ?? 'upload';

// Validate CSRF token for delete actions
if (in_array($action, ['delete_temp', 'delete_existing']) && !hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}

// Define upload directory
$uploadDir = $basePath . '/Uploads/';
$uploadUrl = '/LatuaGroup/Uploads/';

// Ensure upload directory exists and is writable
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        error_log("Failed to create upload directory: $uploadDir");
        echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
        exit();
    }
}
if (!is_writable($uploadDir)) {
    error_log("Upload directory is not writable: $uploadDir");
    echo json_encode(['success' => false, 'error' => 'Upload directory is not writable']);
    exit();
}

// Log request details for debugging
error_log("Received request: action=$action, POST=" . print_r($_POST, true) . ", FILES=" . print_r($_FILES, true));

// Handle delete temporary image
if ($action === 'delete_temp' && isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT image_path FROM property_images_temp WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $file_path = $basePath . '/Uploads/' . $image['image_path'];
            if (file_exists($file_path)) {
                if (!unlink($file_path)) {
                    error_log("Failed to delete temporary image file: $file_path");
                }
            } else {
                error_log("Temporary image file not found: $file_path");
            }

            $stmt_delete = $pdo->prepare("DELETE FROM property_images_temp WHERE id = ?");
            $stmt_delete->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Gambar sementara berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gambar tidak ditemukan di data sementara.']);
        }
    } catch (PDOException $e) {
        error_log("Database error in delete_temp: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
    }
    exit();
}

// Handle delete existing image
if ($action === 'delete_existing' && isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $file_path = $basePath . '/Uploads/' . $image['image_path'];
            if (file_exists($file_path)) {
                if (!unlink($file_path)) {
                    error_log("Failed to delete existing image file: $file_path");
                }
            } else {
                error_log("Existing image file not found: $file_path");
            }

            $stmt_delete = $pdo->prepare("DELETE FROM property_images WHERE id = ?");
            $stmt_delete->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Gambar berhasil dihapus permanen.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gambar tidak ditemukan di data properti.']);
        }
    } catch (PDOException $e) {
        error_log("Database error in delete_existing: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
    }
    exit();
}

// Handle image upload
if ($action === 'upload') {
    if (isset($_FILES['images']) && is_array($_FILES['images']['error'])) {
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $responses = [];

        foreach ($_FILES['images']['error'] as $index => $error) {
            if ($error == UPLOAD_ERR_OK) {
                // Validate file size
                if ($_FILES['images']['size'][$index] > $maxFileSize) {
                    $responses[] = ['success' => false, 'error' => 'Ukuran file terlalu besar untuk ' . $_FILES['images']['name'][$index] . '. Maksimum 5MB.'];
                    continue;
                }

                // Validate file type
                $file_info = pathinfo($_FILES['images']['name'][$index]);
                $file_extension = strtolower($file_info['extension']);
                if (!in_array($file_extension, $allowed_extensions)) {
                    $responses[] = ['success' => false, 'error' => 'Tipe file tidak diizinkan untuk ' . $_FILES['images']['name'][$index] . '. Gunakan jpg, jpeg, png, gif, atau webp.'];
                    continue;
                }

                // Validate image content
                if (!getimagesize($_FILES['images']['tmp_name'][$index])) {
                    $responses[] = ['success' => false, 'error' => 'File bukan gambar valid: ' . $_FILES['images']['name'][$index]];
                    continue;
                }

                // Generate unique filename
                $new_file_name = 'prop_' . uniqid() . '.' . $file_extension;
                $upload_path = $uploadDir . $new_file_name;
                $db_path = $new_file_name;

                // Move uploaded file
                if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $upload_path)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO property_images_temp (image_path) VALUES (?)");
                        $stmt->execute([$db_path]);
                        $image_id = $pdo->lastInsertId();

                        $responses[] = [
                            'success' => true,
                            'image_id' => $image_id,
                            'image_path' => $db_path
                        ];
                    } catch (PDOException $e) {
                        if (file_exists($upload_path)) {
                            unlink($upload_path);
                        }
                        error_log("Database error in upload: " . $e->getMessage());
                        $responses[] = ['success' => false, 'error' => 'Gagal menyimpan ke database untuk ' . $_FILES['images']['name'][$index] . ': ' . htmlspecialchars($e->getMessage())];
                    }
                } else {
                    error_log("Failed to move uploaded file to: $upload_path");
                    $responses[] = ['success' => false, 'error' => 'Gagal memindahkan file ' . $_FILES['images']['name'][$index]];
                }
            } else {
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in form',
                    UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload',
                ];
                $error_message = $error_messages[$error] ?? 'Unknown upload error';
                $responses[] = ['success' => false, 'error' => 'Error saat mengunggah ' . ($_FILES['images']['name'][$index] ?? 'unknown file') . '. Error: ' . $error_message];
            }
        }

        // Return the first response for compatibility with existing JavaScript
        echo json_encode($responses[0] ?? ['success' => false, 'error' => 'No valid files uploaded']);
        exit();
    } else {
        error_log("No files received or invalid file input. FILES=" . print_r($_FILES, true) . ", POST=" . print_r($_POST, true));
        echo json_encode(['success' => false, 'error' => 'Tidak ada file yang diunggah atau input file tidak valid']);
    }
    exit();
}

// Invalid action
error_log("Invalid action received: $action");
echo json_encode(['success' => false, 'error' => 'Aksi tidak valid: ' . htmlspecialchars($action)]);
?>