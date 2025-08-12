<?php
// No whitespace before this
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asia One Property</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- START HEADER STYLES --- */
        body {
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px 40px;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            display: block;
        }
        
        .logo img {
            height: 90px;
            width: auto;
        }

        .header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 15px;
        }
        
        .top-bar {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .contact-info .email {
            font-size: 16px;
            color: #4a4a4a;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .contact-info .email i {
            margin-right: 8px;
            color: #5d3a6c;
        }

        .social-media a {
            font-size: 24px;
            margin: 0 6px;
            transition: opacity 0.3s;
        }
        .social-media a:hover {
            opacity: 0.7;
        }

        .social-media .fa-facebook { color: #1877F2; }
        .social-media .fa-instagram { color: #E4405F; }
        .social-media .fa-youtube { color: #FF0000; }

        .nav-buttons {
            display: flex;
            gap: 12px;
        }

        .nav-button {
            background-color: #5d3a6c;
            color: #fff;
            padding: 10px 22px;
            font-size: 15px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
            transition: background-color 0.3s;
        }
        .nav-button:hover {
            background-color: #7b5a8a;
        }

        /* --- STYLES UNTUK TAMPILAN MOBILE --- */
        @media (max-width: 768px) {
            /* 1. Atur header utama untuk menumpuk vertikal & di tengah */
            .header {
                flex-direction: column;
                gap: 25px; /* Jarak antara logo dan konten di bawahnya */
                padding: 25px 20px;
            }

            .logo img {
                height: 80px; /* Ukuran logo di mobile */
            }
            
            /* 2. Atur blok kanan agar tampil & kontennya di tengah */
            .header-right {
                display: flex; /* Pastikan ini 'flex', bukan 'none' */
                align-items: center; /* Konten di tengah */
                gap: 25px;
            }

            /* 3. Atur baris atas (email & sosmed) agar menumpuk vertikal */
            .top-bar {
                flex-direction: column;
                gap: 20px;
            }

            /* 4. Atur tombol navigasi agar menjadi 2x2 */
            .nav-buttons {
                flex-wrap: wrap; /* Ini kuncinya agar tombol bisa turun ke bawah */
                justify-content: center;
                max-width: 320px; /* Batasi lebar agar 2 tombol per baris */
                gap: 15px;
            }

            .nav-button {
                padding: 10px 15px;
                font-size: 14px;
                flex-basis: 120px; /* Beri basis lebar untuk setiap tombol */
                text-align: center;
            }
        }
        /* --- END HEADER STYLES --- */
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="/LatuaGroup/index.php">
                <img src="/LatuaGroup/uploads/latualogo.jpg" alt="Asia One Property Logo">
            </a>
        </div>
        
        <div class="header-right">
            <div class="top-bar">
                <div class="contact-info">
                    <span class="email"><i class="fas fa-envelope"></i> cs@asiaone.co.id</span>
                </div>
                <div class="social-media">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="nav-buttons">
                <a href="/LatuaGroup/index.php" class="nav-button">Home</a>
                <a href="/LatuaGroup/about.php" class="nav-button">Tentang Kami</a>
                <a href="/LatuaGroup/office.php" class="nav-button">Kantor Kami</a>
                <a href="/LatuaGroup/contact.php" class="nav-button">Hubungi Kami</a>
            </div>
        </div>
    </header>

    <div class="container"> 
        </div>
</body>
</html>