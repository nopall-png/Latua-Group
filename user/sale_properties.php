<?php
include '../includes/db_connect.php';
include '../includes/header.php';

// Konfigurasi Paginasi
$properties_per_page = 9; // Jumlah properti per halaman
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $properties_per_page;

$property_type = 'for_sale'; // Tipe properti spesifik untuk halaman ini

// 1. Hitung Total Properti untuk tipe ini
$count_stmt = $pdo->prepare("SELECT COUNT(id) FROM properties WHERE property_type = ?");
$count_stmt->execute([$property_type]);
$total_properties = $count_stmt->fetchColumn();

// 2. Hitung Total Halaman
$total_pages = ceil($total_properties / $properties_per_page);

// 3. Ambil Properti untuk Halaman Saat Ini dengan gambar pertama
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.description, 
        p.price, 
        p.id_properti,
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
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $property_type, PDO::PARAM_STR);
$stmt->bindValue(2, $properties_per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$properties = $stmt->fetchAll();

?>

<div class="container">
    <h1>Properties for Sale</h1>
    <div class="property-grid">
        <?php if (empty($properties)): ?>
            <p>Tidak ada properti untuk dijual saat ini.</p>
        <?php else: ?>
            <?php foreach ($properties as $property): ?>
                <a href="../detail_property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                    <div class="property-card">
                        <img src="../Uploads/<?php echo htmlspecialchars($property['main_image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p><?php echo htmlspecialchars($property['description']); ?></p>
                        <p>Price: $<?php echo number_format($property['price'], 2); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="page-link prev-next">&laquo; Prev</a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            if ($start_page > 1) {
                echo '<a href="?page=1" class="page-link">1</a>';
                if ($start_page > 2) {
                    echo '<span class="page-dots">...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                    <span class="page-dots">...</span>
                <?php endif; ?>
                <a href="?page=<?php echo $total_pages; ?>" class="page-link"><?php echo $total_pages; ?></a>
            <?php endif; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="page-link prev-next">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>