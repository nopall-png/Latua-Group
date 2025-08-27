<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.price, p.province, p.regency, p.property_type,
               pi.image_path AS main_image_path
        FROM properties p
        LEFT JOIN (
            SELECT property_id, MIN(id) AS min_img_id 
            FROM property_images GROUP BY property_id
        ) AS min_images ON p.id = min_images.property_id
        LEFT JOIN property_images pi ON min_images.min_img_id = pi.id
        ORDER BY p.created_at DESC
    ");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // tambahkan image_url agar langsung bisa dipakai di React
    foreach ($properties as &$prop) {
        $prop['image_url'] = !empty($prop['main_image_path'])
            ? "/LatuaGroup/uploads/properties/" . $prop['main_image_path']
            : "/LatuaGroup/uploads/default.jpg";
    }

    echo json_encode($properties);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
