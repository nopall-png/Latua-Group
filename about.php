<?php include 'includes/header.php'; ?>

<style>
    /* Tambahan CSS khusus untuk halaman about_us.php */
    .hero-image-about {
        position: relative;
        width: 100%;
        height: 400px; /* Tinggi hero section */
        background-image: url('/Latua-Group/uploads/img_688cb59c3dd275.00555717.jpg');
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

    /* Bagian Konten Tentang Kami */
    .about-content {
        padding: 50px 20px;
        background-color: #f5f5f5;
        text-align: center;
    }
    
    .about-card {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    .about-card h2 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 20px;
    }
    
    .about-card p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
        text-align: left;
    }
    
    .about-card ul {
        list-style-type: none;
        padding: 0;
        text-align: left;
        margin-bottom: 15px;
    }
    
    .about-card ul li {
        margin-bottom: 10px;
    }
    
    .about-card .contact-link {
        color: #007bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .about-card .contact-link:hover {
        text-decoration: underline;
    }

    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .hero-image-about {
            height: 300px;
        }
        .hero-title {
            font-size: 2rem;
        }
        .breadcrumb-nav {
            padding: 10px 20px;
        }
        .about-card {
            padding: 20px;
        }
        .about-card h2 {
            font-size: 1.3rem;
        }
        .about-card p {
            font-size: 0.9rem;
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
        <h2>Tentang Asia One Property</h2>
        <p>Asia One Property adalah agen properti terdaftar yang merupakan anggota AREBI (Asosiasi Real Estate Broker Indonesia) dan memiliki surat izin broker SIUP P4.</p>
        <p>Kami berdiri sejak 9 Mei 2016 dengan komitmen untuk menghubungkan kebutuhan para pembeli, penyewa, dan investor dengan pengembang atau pemilik properti yang ingin memasarkan properti mereka. Kami menyediakan berbagai jenis properti, mulai dari rumah, apartemen, tanah, gudang, hingga ruko/kantor dan bangunan komersial lainnya.</p>
        <p>Selain itu, kami juga menyediakan fasilitas tambahan kepada klien kami, seperti akses ke layanan perbankan, notaris, dan kebutuhan pendukung lainnya yang penting dalam transaksi pembelian atau penyewaan properti. Dengan pengalaman dan jaringan yang kuat, kami siap membantu Anda mencapai tujuan properti Anda.</p>

        <h2>Mitra Bank</h2>
        <p>Berikut adalah beberapa bank yang telah bermitra dengan kami:</p>
        <ul>
            <li>Bank Central Asia</li>
            <li>Bank Permata</li>
            <li>Bank Mandiri</li>
            <li>Bank Syariah Indonesia</li>
            <li>Bank BTN</li>
            <li>Bank BRI</li>
            <li>Bank BNI</li>
            <li>Bank CIMB Niaga</li>
            <li>Bank OCBC NISP</li>
            <li>Bank UOB</li>
            <li>Bank Panin</li>
            <li>Bank Ganesha</li>
            <li>Bank Artha Graha</li>
            <li>Bank Danamon</li>
            <li>Bank Index</li>
            <li>KB Bukopin</li>
            <li>China Construction Bank</li>
            <li>Commonwealth Bank</li>
            <li>Dan beberapa bank lainnya.</li>
        </ul>
        <p>Kami juga telah bekerja sama dengan beberapa kantor notaris di sekitar area properti yang kami pasarkan.</p>

        <h2>Proyek Utama</h2>
        <p>Beberapa proyek di mana kami telah menjadi Lead Agent:</p>
        <ul>
            <li><strong>2016:</strong> Jayakarta Group untuk AZALEA Apartemen di Cikarang, Lavanya Hills Residence di Cinere, Jakarta Selatan, Lead Agent untuk Technopolis di Karawang, Lead Agent untuk Jakarta-Indonesia untuk proyek Singapura di Pulau Bintan: The Haven Premier Bintan</li>
            <li><strong>2019:</strong> Lead Agent untuk Apartemen URBAN SKY by Urban Jakarta Propertindo di TOD Stasiun LRT Cikunir, Lead Agent untuk Sky Suites apartemen, Kuningan-Jakarta, Lead Agent untuk The Lana-Alam Sutera, proyek dari Wing Tai Group - Brewin Mesa Development</li>
        </ul>

        <h2>Penghargaan</h2>
        <p>Beberapa penghargaan yang telah kami terima:</p>
        <ul>
            <li><strong>2016:</strong> Djayakarta Group – Best Selling Azalea Suites Apartment dan Green Palace Residence, Lavanya Garden Residence – 3rd The Best Office</li>
            <li><strong>2017:</strong> Pollux Properties – Top 10 Agent Coordinator Gangnam District, Summarecon Annual Award – The Best Active Agent, Daan Mogot City – The Best Lead Agent Broker</li>
            <li><strong>2018:</strong> Chadstone Apartment – 2nd Best Selling Agency, Pollux Properties – Top 10 Agent Coordinator Property in Bekasi, Lavanya Garden Residence – Top 3 Best Office</li>
            <li><strong>2020:</strong> Summarecon Annual Award – #2 Best Selling Agent of the year 2019</li>
            <li><strong>2021:</strong> Summarecon Annual Award – Most Active Agent of the year 2020</li>
            <li><strong>2022:</strong> Summarecon Annual Award – 3rd Best Selling Agent 2021, Lamudi.co.id – Most Valuable Property Office, Duta Putra Land Project Bintaro Park View – 2nd Winner Top Sales Agent Office, Duta Putra Land Project Bintaro Park View – 1st Winner Top Sales Agent, BTN Marketing of The Year 2022 – 1st Winner, ISPI Group Project Mutiara Gading City – 4th Top Office Agent Property, ISPI Group Project Mutiara Gading City – 5th Top Lead Agent Property</li>
        </ul>

        <h2>Mitra Pengembang</h2>
        <p>Asia One Property juga telah memiliki kontrak kerjasama (MOU) dengan pengembang:</p>
        <ul>
            <li>Summarecon</li>
            <li>Duta Putra Land</li>
            <li>Ciputra</li>
            <li>Jakarta Garden City</li>
            <li>Alam Sutera</li>
            <li>Modernland</li>
            <li>ASYA, Astra Property</li>
            <li>Keppel Land</li>
            <li>Paramount</li>
            <li>BSD</li>
            <li>Sinarmas Land</li>
            <li>Lavon</li>
            <li>Metland</li>
            <li>Vasanta Cibitung</li>
            <li>Dan beberapa pengembang lainnya…</li>
        </ul>

        <p>Kami juga memberikan pelatihan kepada Marketing Agent kami agar mereka selalu dapat memberikan pelayanan yang terbaik dan profesional kepada klien kami. Semua ini bertujuan untuk selalu memberikan kepuasan kepada semua pihak sehingga hasil yang diperoleh adalah yang terbaik.</p>
        <p><strong>Salam Sejahtera,</strong></p>
        <p><strong>Ellen Novita</strong><br>Principal<br>Asia One Property</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
