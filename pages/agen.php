<?php
require __DIR__ . '/../includes/db_connect.php';
require __DIR__ . '/../includes/header.php';

// Fetch agents from database
$agents = [];
try {
    $stmt_agents = $pdo->query("SELECT * FROM agents ORDER BY name ASC");
    $agents = $stmt_agents->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agents = [];
    error_log("Error fetching agents: " . $e->getMessage());
    echo "<div class='container mx-auto px-4 py-6'><p class='text-red-600'>Terjadi kesalahan saat mengambil data agen.</p></div>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Agen - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="font-lato bg-gray-100 text-gray-800">
    <div class="bg-white py-12 px-5 text-center">
        <p class="text-lg md:text-xl font-semibold text-gray-800 mb-5">Cari Agen untuk membantu anda menemukan properti impian</p>
        <div class="flex flex-col md:flex-row justify-center gap-4 max-w-xl mx-auto">
            <input type="text" id="agent-search" placeholder="Cari nama agen..." class="w-full md:w-3/4 p-3 border border-gray-300 rounded-full text-base focus:border-blue-800 focus:ring-2 focus:ring-blue-200 outline-none transition">
            <button id="search-button" class="w-full md:w-auto px-6 py-3 bg-blue-800 text-white rounded-full font-bold text-base hover:bg-blue-900 hover:-translate-y-1 transition">Cari</button>
        </div>
    </div>

    <div class="py-16 px-5 text-center">
        <?php if (empty($agents)): ?>
            <p class="text-gray-600 text-base">Tidak ada agen yang tersedia saat ini.</p>
        <?php else: ?>
            <div class="agent-card-container max-w-6xl mx-auto flex flex-wrap justify-center gap-8">
                <?php foreach ($agents as $agent): ?>
                    <div class="agent-card flex-1 min-w-[300px] bg-white rounded-xl shadow-lg p-8 text-center hover:-translate-y-2 hover:shadow-xl transition">
                        <div class="w-36 h-36 mx-auto mb-5 rounded-full overflow-hidden border-4 border-blue-800 shadow-md">
                            <?php if ($agent['photo_path'] && file_exists(__DIR__ . '/../Uploads/agents/' . $agent['photo_path'])): ?>
                                <img src="../Uploads/agents/<?php echo htmlspecialchars($agent['photo_path']); ?>" alt="Foto Agen <?php echo htmlspecialchars($agent['name']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user-circle text-7xl text-blue-800 flex items-center justify-center w-full h-full"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="agent-name text-xl md:text-2xl font-bold text-blue-800 mb-4"><?php echo htmlspecialchars($agent['name']); ?></h3>
                        <div class="agent-contact-info space-y-2">
                            <p class="text-base text-gray-600"><i class="fas fa-phone mr-2 text-blue-800"></i><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                            <?php if (!empty($agent['email'])): ?>
                                <p class="text-base text-gray-600"><i class="fas fa-envelope mr-2 text-blue-800"></i><a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>" class="text-blue-800 hover:text-blue-900 hover:underline"><?php echo htmlspecialchars($agent['email']); ?></a></p>
                            <?php endif; ?>
                            <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $agent['phone_number'])); ?>" target="_blank" class="inline-flex items-center justify-center gap-2 mt-5 px-6 py-3 bg-green-500 text-white rounded-full font-bold text-base hover:bg-green-600 hover:-translate-y-1 transition shadow-md">
                                <i class="fab fa-whatsapp text-lg"></i> Chat WhatsApp
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('agent-search');
        const searchButton = document.getElementById('search-button');
        const agentCards = document.querySelectorAll('.agent-card');

        function filterAgents() {
            const searchText = searchInput.value.toLowerCase();
            
            agentCards.forEach(card => {
                const agentName = card.querySelector('.agent-name').textContent.toLowerCase();
                card.style.display = agentName.includes(searchText) ? 'block' : 'none';
            });
        }

        searchButton.addEventListener('click', filterAgents);
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                filterAgents();
            }
        });
    });
    </script>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>