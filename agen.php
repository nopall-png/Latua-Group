<?php
include 'includes/db_connect.php';
include 'includes/header.php';

// Ambil data agen dari database
$agents = [];
try {
    $stmt_agents = $pdo->query("SELECT * FROM agents ORDER BY name ASC");
    $agents = $stmt_agents->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agents = [];
    error_log("Error fetching agents: " . $e->getMessage());
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f0f2f5;
        color: #333;
    }
    
    /* Bagian atas halaman tanpa background */
    .search-section {
        padding: 50px 20px;
        background-color: #ffffff;
        text-align: center;
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
        margin: 10px 0 20px;
        color: #333;
        font-weight: 600;
    }

    /* CSS untuk search box */
    .search-box {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        width: 100%;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .search-box input {
        width: 70%;
        padding: 14px 20px;
        border: 1px solid #ddd;
        border-radius: 50px;
        outline: none;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .search-box input:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 8px rgba(44, 62, 80, 0.2);
    }
    
    .search-box button {
        padding: 14px 30px;
        background-color: #2c3e50;
        color: white;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    
    .search-box button:hover {
        background-color: #34495e;
        transform: translateY(-2px);
    }

    /* Bagian Konten Agen */
    .agent-content {
        padding: 70px 20px;
        background-color: #f0f2f5;
        text-align: center;
    }
    
    .agent-card-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
    }
    
    .agent-card {
        flex: 1;
        min-width: 300px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .agent-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    .agent-photo-container {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #2c3e50;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .agent-photo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .agent-photo-container .no-photo-icon {
        font-size: 100px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    
    .agent-name {
        font-size: 1.8rem;
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .agent-contact-info p {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 10px;
    }
    
    /* Perbaikan warna untuk teks email */
    .agent-contact-info a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .agent-contact-info a:hover {
        color: #34495e;
        text-decoration: underline;
    }
    
    /* CSS baru untuk tombol WhatsApp yang lebih baik */
    .btn-whatsapp {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        padding: 12px 25px;
        background: linear-gradient(45deg, #10C65A, #0D994B);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(16, 198, 90, 0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .btn-whatsapp:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(16, 198, 90, 0.6);
    }
    
    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .search-box input, .search-box button {
            width: 100%;
        }
        .search-box {
            flex-direction: column;
        }
    }
</style>

<div class="search-section">
    <p class="hero-subtitle">Cari Agen untuk membantu anda menemukan properti impian</p>
    <div class="search-box">
        <input type="text" id="agent-search" placeholder="Cari nama agen...">
        <button id="search-button">Cari</button>
    </div>
</div>

<div class="agent-content">
    <?php if (empty($agents)): ?>
        <p>Tidak ada agen yang tersedia saat ini.</p>
    <?php else: ?>
        <div class="agent-card-container">
            <?php foreach ($agents as $agent): ?>
                <div class="agent-card">
                    <div class="agent-photo-container">
                        <?php if ($agent['photo_path'] && file_exists('Uploads/agents/' . $agent['photo_path'])): ?>
                            <img src="Uploads/agents/<?php echo htmlspecialchars($agent['photo_path']); ?>" alt="Foto Agen <?php echo htmlspecialchars($agent['name']); ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen input pencarian dan tombol
    const searchInput = document.getElementById('agent-search');
    const searchButton = document.getElementById('search-button');
    const agentCards = document.querySelectorAll('.agent-card');

    function filterAgents() {
        // Ambil nilai input dan ubah ke huruf kecil
        const searchText = searchInput.value.toLowerCase();
        
        agentCards.forEach(card => {
            // Ambil nama agen dari elemen h3
            const agentName = card.querySelector('.agent-name').textContent.toLowerCase();
            
            // Periksa apakah nama agen mengandung teks pencarian
            if (agentName.includes(searchText)) {
                card.style.display = 'block'; // Tampilkan kartu jika cocok
            } else {
                card.style.display = 'none'; // Sembunyikan kartu jika tidak cocok
            }
        });
    }

    // Tambahkan event listener untuk tombol "Cari"
    searchButton.addEventListener('click', filterAgents);

    // Tambahkan event listener untuk menekan tombol 'Enter' pada input
    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            filterAgents();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
