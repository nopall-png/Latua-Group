<?php include 'includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* CSS Umum */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #ffffff; /* Menggunakan putih sebagai latar belakang */
        color: #111; /* Menggunakan warna yang mendekati hitam untuk teks */
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    /* Hero Section */
    .hero-image-about {
        position: relative;
        width: 100%;
        height: 70vh; /* More height and responsive */
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
        /* Gaya awal untuk animasi */
        opacity: 0;
        transform: translateY(50px);
        animation: slideInAndFade 1s ease-out forwards;
        animation-delay: 0.5s;
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
    
    /* Animasi */
    @keyframes slideInAndFade {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Bagian Konten Tentang Kami */
    .about-content {
        padding: 70px 20px;
        background-color: #ffffff; /* Menggunakan putih sebagai latar belakang konten */
    }
    
    .about-card {
        max-width: 1000px;
        margin: 0 auto;
        padding: 50px;
    }
    
    .about-card h2 {
        font-size: 2.5rem;
        color: #111; /* Menggunakan warna hitam untuk judul */
        margin-bottom: 20px;
        font-weight: 700;
        text-align: center;
    }
    
    .about-card .subtitle {
        font-size: 1.2rem;
        color: #111; /* Menggunakan warna hitam untuk sub judul */
        margin-bottom: 25px;
        font-style: italic;
        text-align: center;
    }
    
    .about-card p {
        font-size: 1rem;
        color: #111; /* Menggunakan warna hitam untuk paragraf */
        line-height: 1.8;
        margin-bottom: 20px;
        text-align: justify;
    }
    
    /* Styling baru untuk bagian Visi Misi yang digabungkan */
    .mission-vision-container {
        display: flex;
        justify-content: space-between;
        gap: 40px;
        margin-top: 40px;
    }
    
    .mission-vision-container .section {
        flex: 1;
    }
    
    .mission-vision-container .section h3 {
        color: #334894; /* Menggunakan warna biru baru */
        font-size: 1.8rem;
        margin-top: 0;
        margin-bottom: 15px;
        font-weight: 600;
        text-align: center;
    }

    .mission-vision-container .section p {
        text-align: center;
        margin: 0;
    }

    /* Styling untuk list */
    .about-list-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 20px;
    }

    .about-list-container .list-section {
        flex: 1;
    }
    
    .about-list {
        list-style-type: none; /* Menghilangkan bullet default */
        padding: 0;
        margin: 0;
        text-align: left; /* Rata kiri untuk list */
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .about-list li {
        margin-bottom: 5px;
        position: relative;
        padding-left: 1.5em;
    }

    /* Custom bullet point */
    .about-list li::before {
        content: '\2022'; /* Unicode untuk bullet point */
        color: #334894; /* Menggunakan warna biru baru */
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }
    
    .about-section-heading {
        color: #334894; /* Menggunakan warna biru baru */
        font-size: 1.8rem;
        font-weight: 600;
        margin-top: 40px;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Responsive untuk desktop */
    @media (min-width: 769px) {
        .about-card p {
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .about-list-container {
            justify-content: center;
        }
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
            text-align: left; /* On mobile, text is left-aligned for easier reading */
        }
        
        .mission-vision-container {
            flex-direction: column; /* Stack vertically on mobile */
            gap: 20px;
        }

        .mission-vision-container .section h3 {
            font-size: 1.5rem;
        }
        
        .about-section-heading {
            font-size: 1.5rem;
        }

        .about-list-container {
            flex-direction: column;
        }
        
        .about-list {
            text-align: left; /* Rata kiri untuk list di mobile */
            display: block;
        }

        .about-list li {
            display: list-item;
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
        <p class="subtitle">Solusi Properti Cerdas, Aman, dan Menguntungkan</p>
        <p>Di Latuea Land, kami memahami bahwa setiap properti bukan sekadar bangunan—tetapi impian, tujuan, dan investasi masa depan.</p>
        <p>Sebagai perusahaan jasa properti yang berpengalaman dan terpercaya, kami hadir untuk membantu Anda dalam proses jual, sewa, maupun beli properti dengan cara yang mudah, cepat, dan menguntungkan.</p>
        
        <div class="mission-vision-container">
            <div class="section">
                <h3>Misi Kami</h3>
                <p>Memberikan solusi properti terbaik dengan integritas tinggi, transparansi, dan pelayanan prima untuk mewujudkan impian properti setiap klien.</p>
            </div>
            <div class="section">
                <h3>Visi Kami</h3>
                <p>Menjadi perusahaan properti terdepan yang diakui sebagai mitra terpercaya dalam menciptakan nilai tambah bagi semua pihak.</p>
            </div>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>
