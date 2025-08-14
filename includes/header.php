<?php
// No whitespace before this
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asia One Property</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- START HEADER STYLES --- */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            margin-top: 0;
            padding-top: 0;
        }

        .header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px 40px;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }

        .logo a {
            display: block;
        }
        
        .logo img {
            height: 230px;
            width: auto;
            max-width: 100%;
        }

        .header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 15px;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        
        .top-bar {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .contact-info .email {
            font-size: 16px;
            color: #000000ff;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .contact-info .email i {
            margin-right: 8px;
            color: #1d2799ff;
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
            background-color: #334894;
            color: #fff;
            padding: 8px 18px;
            font-size: 14px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
            transition: background-color 0.3s;
        }
        .nav-button:hover {
            background-color: #4a5eb8;
        }

        /* --- STYLES UNTUK TAMPILAN MOBILE --- */
        @media (max-width: 768px) {
            /* 1. Atur header utama untuk menumpuk vertikal & di tengah */
            .header {
                flex-direction: column;
                gap: 20px;
                padding: 20px 20px;
                margin-bottom: 0px;
                min-height: 300px;
            }

            .logo img {
                height: 120px;
                max-width: 100%;
            }
            
            /* 2. Atur blok kanan agar tampil & kontennya di tengah */
            .header-right {
                display: flex;
                align-items: center;
                gap: 20px;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            /* 3. Atur baris atas (email & sosmed) agar menumpuk vertikal */
            .top-bar {
                flex-direction: column;
                gap: 15px;
            }

            /* 4. Atur tombol navigasi agar menjadi 2x2 */
            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
                max-width: 320px;
                gap: 12px;
            }

            .nav-button {
                padding: 8px 12px;
                font-size: 13px;
                flex-basis: 120px;
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
                    <span class="email"><i class="fas fa-envelope"></i>latuealand@gmail.com</span>
                </div>
                <div class="social-media">
                    <a href="https://www.instagram.com/latuealand/?igsh=OWRhYTd6am42cjly&utm_source=qr#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="nav-buttons">
                <a href="/LatuaGroup/" class="nav-button">Home</a>
                <a href="/LatuaGroup/about" class="nav-button">Tentang Kami</a>
                <a href="/LatuaGroup/office" class="nav-button">Kantor Kami</a>
                <a href="/LatuaGroup/contact" class="nav-button">Hubungi Kami</a>
            </div>
        </div>
    </header>
</body>
</html>