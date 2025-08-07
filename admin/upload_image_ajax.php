<?php
// admin/upload_image_ajax.php

// PENTING: Mulai session di awal script ini
session_start(); 

include '../includes/db_connect.php'; // Sesuaikan jalur db_connect.php

header('Content-Type: application/json'); // Respons JSON

$response = [
    'success' => false,
    'image_path' => null,
    'image_id' => null,
    'error' => 'Unknown error.'
];

// Pastikan admin login
if (!isset($_SESSION['user_id'])) { 
    $response['error'] = 'Unauthorized access. User not logged in.';
    echo json_encode($response);
    exit();
}

$target_dir = "../Uploads/"; // Sesuaikan jalur folder uploads
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0755, true)) {
        $response['error'] = 'Failed to create uploads directory. Check permissions.';
        echo json_encode($response);
        exit();
    }
}

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $file_tmp_name = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid('img_', true) . '.' . $file_extension;
    $upload_path = $target_dir . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $upload_path)) {
        try {
            // Simpan gambar ke tabel TEMPORARY dulu (penting untuk AJAX)
            $stmt = $pdo->prepare("INSERT INTO property_images_temp (image_path) VALUES (?)");
            $stmt->execute([$new_file_name]);
            $temp_image_id = $pdo->lastInsertId();

            $response['success'] = true;
            $response['image_path'] = $new_file_name;
            $response['image_id'] = $temp_image_id; // Kirim ID gambar sementara
            $response['message'] = 'Image uploaded temporarily.';
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
            // Hapus file yang sudah terupload jika ada masalah DB
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
        }
    } else {
        $response['error'] = 'Failed to move uploaded file. Check directory permissions or file size limits.';
    }
} else {
    $response['error'] = 'No image uploaded or upload error code: ' . ($_FILES['image']['error'] ?? 'N/A');
    if (isset($_FILES['image'])) {
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $response['error'] .= ' (The uploaded file exceeds the upload_max_filesize directive in php.ini)';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $response['error'] .= ' (The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form)';
                break;
            case UPLOAD_ERR_PARTIAL:
                $response['error'] .= ' (The uploaded file was only partially uploaded)';
                break;
            case UPLOAD_ERR_NO_FILE:
                $response['error'] .= ' (No file was uploaded)';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $response['error'] .= ' (Missing a temporary folder)';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $response['error'] .= ' (Failed to write file to disk)';
                break;
            case UPLOAD_ERR_EXTENSION:
                $response['error'] .= ' (A PHP extension stopped the file upload)';
                break;
        }
    }
}

echo json_encode($response);
exit();
?>