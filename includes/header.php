<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asia One Property</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- START HEADER STYLES --- */
        .header {
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            padding: 10px 10px;
            text-align: center;
            position: relative;
            z-index: 1000;
        }

        .logo img {
            height: 360px;
            width: auto;
            margin-bottom: 5px; /* Jarak lebih dekat dengan email */
        }

        .contact-info {
            margin-bottom: 10px;
        }

        .contact-info .email {
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-info .email i {
            margin-right: 6px;
        }

        .social-media {
            margin-bottom: 15px;
        }

        .social-media a {
            font-size: 16px;
            color: #666;
            margin: 0 8px;
            transition: color 0.3s ease;
        }

        .social-media a:hover {
            color: #333;
        }

        .social-media .fa-facebook { color: #3b5998; }
        .social-media .fa-instagram { color: #E1306C; }
        .social-media .fa-youtube { color: #ff0000; }

        .nav-buttons {
            display: flex;
            flex-wrap: nowrap; /* Pastikan tombol tetap di satu baris */
            overflow-x: auto; /* Tambahkan scrolling horizontal jika tombol tidak muat */
            justify-content: center;
            gap: 8px;
            white-space: nowrap; /* Pastikan teks tombol tidak membungkus */
        }

        .nav-buttons::-webkit-scrollbar {
            display: none; /* Sembunyikan scrollbar untuk tampilan bersih */
        }

        .nav-button {
            background-color: #334894; /* Warna tema sesuai #334894 */
            color: #fff;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }

        .nav-button:hover {
            background-color: #4a5fb3; /* Hover warna sesuai tema */
            transform: translateY(-1px);
        }

        /* Responsif */
        @media (max-width: 768px) {
            .header {
                padding: 8px 10px;
            }

            .logo img {
                height: 200px; /* Kurangi ukuran logo di mobile */
            }

            .contact-info .email {
                font-size: 12px;
            }

            .social-media a {
                font-size: 14px;
                margin: 0 6px;
            }

            .nav-buttons {
                gap: 6px; /* Kurangi jarak antar tombol di mobile */
            }

            .nav-button {
                padding: 6px 12px; /* Kurangi padding di mobile */
                font-size: 11px; /* Kurangi ukuran font di mobile */
            }
        }

        @media (min-width: 768px) {
            .header {
                padding: 15px 30px;
            }

            .logo img {
                height: 450px;
            }

            .contact-info .email {
                font-size: 16px;
            }

            .social-media a {
                font-size: 18px;
                margin: 0 10px;
            }

            .nav-button {
                padding: 10px 20px;
                font-size: 14px;
            }

            .nav-buttons {
                gap: 12px;
            }
        }
        /* --- END HEADER STYLES --- */
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="/LatuaGroup/index.php">
                <img src="/LatuaGroup/latualogo.png" alt="Latuea Gorup Logo">
            </a>
        </div>
        <div class="contact-info">
            <span class="email"><i class="fa-solid fa-envelope"></i> cs@asiaone.co.id</span>
        </div>
        <div class="social-media">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
        <div class="nav-buttons">
            <a href="/LatuaGroup/index.php" class="nav-button">Home</a>
            <a href="/LatuaGroup/about.php" class="nav-button">Tentang Kami</a>
            <a href="/LatuaGroup/office.php" class="nav-button">Kantor Kami</a>
            <a href="/LatuaGroup/contact.php" class="nav-button">Hubungi Kami</a>
        </div>
    </div>
    <div class="container">