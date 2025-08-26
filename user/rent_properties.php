<?php
// Include dependencies
require __DIR__ . '/../includes/db_connect.php';
require __DIR__ . '/../includes/header.php';

// Pagination configuration
$properties_per_page = 9;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $properties_per_page;

$property_type = 'for_rent';

// Count total properties
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(id) FROM properties WHERE property_type = ?");
    $count_stmt->execute([$property_type]);
    $total_properties = $count_stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error counting properties: " . $e->getMessage());
    echo "<div class='container mx-auto px-4 py-6'><p class='text-red-600'>Terjadi kesalahan saat mengambil data properti.</p></div>";
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Calculate total pages
$total_pages = ceil($total_properties / $properties_per_page);

// Fetch properties for current page
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.price, 
        p.province, 
        p.regency, 
        p.luas_tanah,
        p.luas_bangunan,
        p.kamar_tidur,
        p.kamar_mandi,
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
    WHERE 
        p.property_type = ?
    ORDER BY 
        p.created_at DESC
    LIMIT ? OFFSET ?
";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $property_type, PDO::PARAM_STR);
    $stmt->bindValue(2, $properties_per_page, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching properties: " . $e->getMessage());
    echo "<div class='container mx-auto px-4 py-6'><p class='text-red-600'>Terjadi kesalahan saat mengambil data properti.</p></div>";
    require __DIR__ . '/../includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties for Rent - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-lato bg-gray-100 text-gray-800">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 text-center mb-3">Properties for Rent</h1>

        <!-- Property Grid -->
        <div class="flex justify-center">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 my-2 w-full">
                <?php if (empty($properties)): ?>
                    <p class="col-span-full text-center text-gray-600 py-5 text-sm md:text-base">Tidak ada properti untuk disewakan saat ini.</p>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?>
                        <a href="detail_property.php?id=<?= htmlspecialchars($property['id']) ?>" class="block h-full">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:-translate-y-1 hover:shadow-lg transition h-full flex flex-col max-w-full sm:max-w-[340px] mx-auto">
                                <div class="relative w-full h-40 sm:h-48">
                                    <img src="../Uploads/properties/<?= htmlspecialchars($property['main_image_path'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                    <div class="absolute bottom-1.5 left-1.5 bg-white text-black px-2 py-1 rounded text-xs md:text-sm font-semibold border border-gray-200 shadow">
                                        Rp <?= number_format($property['price'], 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="p-3 flex-grow">
                                    <h3 class="text-base md:text-lg font-semibold text-gray-800 truncate mb-2"><?= htmlspecialchars($property['title']) ?></h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-2 mt-2">
                                        <div class="flex items-center gap-2 text-xs md:text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                            <i class="fas fa-ruler-combined text-blue-600"></i>
                                            <span><?= htmlspecialchars($property['luas_tanah'] ?? 'N/A') ?> m²</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs md:text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                            <i class="fas fa-home text-blue-600"></i>
                                            <span><?= htmlspecialchars($property['luas_bangunan'] ?? 'N/A') ?> m²</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs md:text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                            <i class="fas fa-bed text-blue-600"></i>
                                            <span><?= htmlspecialchars($property['kamar_tidur'] ?? 'N/A') ?></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs md:text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                            <i class="fas fa-bath text-blue-600"></i>
                                            <span><?= htmlspecialchars($property['kamar_mandi'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- WhatsApp Chat Button -->
        <a href="https://wa.me/62123456789" class="fixed bottom-3 right-3 bg-green-500 text-white px-3 py-2 rounded-full flex items-center gap-2 shadow-lg hover:bg-green-600 hover:-translate-y-1 transition z-10 text-sm md:text-base">
            <i class="fab fa-whatsapp text-base md:text-lg"></i> Butuh bantuan? Chat
        </a>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-4 gap-1 flex-wrap">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="px-2 py-1 border border-gray-300 rounded text-blue-800 text-sm hover:bg-blue-800 hover:text-white hover:border-blue-800 transition">&laquo; Prev</a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                if ($start_page > 1): ?>
                    <a href="?page=1" class="px-2 py-1 border border-gray-300 rounded text-blue-800 text-sm hover:bg-blue-800 hover:text-white hover:border-blue-800 transition">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="px-2 py-1 text-gray-600 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?page=<?= $i ?>" class="px-2 py-1 border border-gray-300 rounded text-sm <?= $i == $current_page ? 'bg-blue-800 text-white border-blue-800' : 'text-blue-800 hover:bg-blue-800 hover:text-white hover:border-blue-800' ?> transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="px-2 py-1 text-gray-600 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?page=<?= $total_pages ?>" class="px-2 py-1 border border-gray-300 rounded text-blue-800 text-sm hover:bg-blue-800 hover:text-white hover:border-blue-800 transition"><?= $total_pages ?></a>
                <?php endif; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="px-2 py-1 border border-gray-300 rounded text-blue-800 text-sm hover:bg-blue-800 hover:text-white hover:border-blue-800 transition">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>