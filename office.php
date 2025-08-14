<?php include 'includes/header.php'; ?>

<style>
    /* Tambahan CSS khusus untuk halaman office.php */
    .hero-image-office {
        position: relative;
        width: 100%;
        height: 400px; /* Tinggi hero section */
        background-image: url('/LatuaGroup/uploads/office.jpg');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin: 0; /* Menghilangkan margin agar full width */
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4); /* Overlay gelap agar teks lebih terbaca */
    }

    .hero-content {
        position: relative;
        z-index: 10;
        padding: 20px;
        /* Gaya awal untuk animasi */
        opacity: 0;
        transform: translateY(50px);
        animation: slideInAndFade 1s ease-out forwards;
        animation-delay: 0.5s;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: bold;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .breadcrumb-nav {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 10px 50px;
        color: white;
        text-align: left;
        background-color: rgba(0, 0, 0, 0.2); /* Latar belakang transparan untuk breadcrumb */
        font-size: 0.9em;
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

    /* Bagian Konten Kantor */
    .office-content {
        padding: 50px 20px;
        background-color: #f5f5f5;
        text-align: center;
    }
    
    .office-card {
        max-width: 400px;
        margin: 0 auto;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    .office-card img {
        max-width: 150px;
        height: auto;
        margin-bottom: 20px;
    }
    
    .office-card h2 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 10px;
    }
    
    .office-card p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 5px;
    }
    
    .office-card .contact-link {
        color: #007bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .office-card .contact-link:hover {
        text-decoration: underline;
    }
    
    .office-card .directions-link {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    
    .office-card .directions-link:hover {
        background-color: #0056b3;
    }


    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .hero-image-office {
            height: 300px;
        }
        .hero-title {
            font-size: 2rem;
        }
        .breadcrumb-nav {
            padding: 10px 20px;
        }
    }
</style>

<div class="hero-image-office">
    <div class="hero-overlay"></div>
    <div class="breadcrumb-nav">
        <a href="/LatuaGroup/">Latua Land</a> / Kantor Kami
    </div>
    <div class="hero-content">
        <h1 class="hero-title">Kantor Kami</h1>
    </div>
</div>

<div class="office-content">
    <div class="office-card">
        <img src="/LatuaGroup/uploads/latua-icon.jpg" alt="Latuae Group Logo">
        <h2>Latue Land</h2>
        <p>Perjuangan<br>Ruko Golden No. 86,<br>Kelurahan Marga Mulya, Bekasi</p>
        <p>Email: <a href="mailto:bekasi.asiaone@gmail.com" class="contact-link">bekasi.asiaone@gmail.com</a></p>
        <p>Telp: <a href="tel:08111952667" class="contact-link">08111952667</a></p>
        <a href="#" class="directions-link">Show Directions</a>
    </div>
</div>
