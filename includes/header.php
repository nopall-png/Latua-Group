<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Web</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- START HEADER STYLES --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 50px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo img {
            height: 60px;
        }

        .header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .contact-social {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .contact-social .email {
            margin-right: 15px;
            font-size: 12px;
            color: #555;
        }

        .contact-social a {
            font-size: 16px;
            color: #555;
            margin-left: 8px;
        }

        .contact-social .fa-facebook { color: #3b5998; }
        .contact-social .fa-instagram { color: #E1306C; }
        .contact-social .fa-youtube { color: #ff0000; }

        .nav-links {
            display: flex;
            gap: 8px;
        }

        .nav-button {
            background-color: #793475;
            color: #fff;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .nav-button:hover {
            background-color: #5a2658;
        }
        /* --- END HEADER STYLES --- */
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="/LatuaGroup/index.php">
                <img src="/LatuaGroup/uploads/latua-icon.jpg" alt="Latuea Land Logo">
            </a>
        </div>
        <div class="header-right">
            <div class="contact-social">
                <span class="email"><i class="fa-solid fa-envelope"></i> latuealand@gmail.com</span>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="nav-links">
                <a href="/LatuaGroup/index.php" class="nav-button">Home</a>
                <a href="/LatuaGroup/about.php" class="nav-button">Tentang Kami</a>
                <a href="/LatuaGroup/office.php" class="nav-button">Kantor Kami</a>
                <a href="/LatuaGroup/contact.php" class="nav-button">Hubungi Kami</a>
            </div>
        </div>
    </div>
    <div class="container">