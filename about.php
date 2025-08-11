<?php include 'includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* CSS Umum */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
    }

    /* Hero Section */
    .hero-image-about {
        position: relative;
        width: 100%;
        height: 70vh; /* Lebih tinggi dan responsif */
        min-height: 400px;
        background-image: url('/LatuaGroup/uploads/about_us.jpg');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin: 0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
    }
    
    .breadcrumb-nav {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 15px 50px;
        color: white;
        text-align: left;
        background-color: rgba(0, 0, 0, 0.3);
        font-size: 1em;
        z-index: 20;
    }
    
    .breadcrumb-nav a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .breadcrumb-nav a:hover {
        color: #ddd;
    }

    /* Bagian Konten Tentang Kami */
    .about-content {
        padding: 70px 20px;
        background-color: #f8f9fa;
    }
    
    .about-card {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        padding: 50px;
        text-align: center;
    }
    
    .about-card h2 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: 700;
    }
    
    .about-card .subtitle {
        font-size: 1.2rem;
        color: #555;
        margin-bottom: 25px;
        font-style: italic;
    }
    
    .about-card p {
        font-size: 1rem;
        color: #555;
        line-height: 1.8;
        margin-bottom: 20px;
        text-align: justify;
    }
    
    .mission-vision {
        display: none; /* Menyembunyikan bagian mission-vision yang lama */
    }

    /* Styling baru untuk bagian Visi Misi yang digabungkan */
    .mission-vision-combined h3 {
        color: #007bff;
        font-size: 1.8rem;
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 600;
        text-align: center; /* Memastikan judul di tengah */
    }

    .mission-vision-combined p {
        text-align: center;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
        .hero-image-about {
            height: 50vh;
            min-height: 300px;
        }
        .hero-title {
            font-size: 2.5rem;
        }
        .breadcrumb-nav {
            padding: 10px 20px;
            font-size: 0.9em;
        }
        .about-card {
            padding: 30px 20px;
        }
        .about-card h2 {
            font-size: 2rem;
        }
        .about-card .subtitle {
            font-size: 1rem;
        }
        .about-card p {
            text-align: left;
        }
        
        .mission-vision-combined h3 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="hero-image-about">
    <div class="hero-overlay"></div>
    <div class="breadcrumb-nav">
        <a href="/Latua-Group/index.php">Latua Land</a> / Tentang Kami
    </div>
    <div class="hero-content">
        <h1 class="hero-title">Tentang Kami</h1>
    </div>
</div>

<div class="about-content">
    <div class="about-card">
        <h2>Selamat Datang di Latuae Land</h2>
        <p>Solusi Properti Cerdas, Aman, dan Menguntungkan</p>
        <p>Di Latuea Land, kami memahami bahwa setiap properti bukan sekadar bangunan—tetapi impian, tujuan, dan investasi masa depan. Sebagai perusahaan jasa properti yang berpengalaman dan terpercaya, kami hadir untuk membantu Anda dalam proses jual, sewa, maupun beli properti dengan cara yang mudah, cepat, dan menguntungkan.</p>
        <p>Kami adalah mitra properti Anda untuk setiap langkah perjalanan.</p>
        
        <div class="mission-vision-combined">
            <h3>Misi Kami</h3>
            <p>Memberikan solusi properti terbaik dengan integritas tinggi, transparansi, dan pelayanan prima untuk mewujudkan impian properti setiap klien.</p>
            <h3>Visi Kami</h3>
            <p>Menjadi perusahaan properti terdepan yang diakui sebagai mitra terpercaya dalam menciptakan nilai tambah bagi semua pihak.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
