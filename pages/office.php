<?php
// Set dynamic title for this page
$page_title = 'Kantor Kami';

// Include header
require __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes slideInAndFade {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-in {
            opacity: 0;
            transform: translateY(2rem);
            animation: slideInAndFade 1s ease-out forwards;
            animation-delay: 0.5s;
        }
    </style>
</head>
<body class="font-lato bg-white text-gray-800">
    <div class="relative w-full h-[400px] md:h-[70vh] min-h-[300px] bg-cover bg-center flex items-center justify-center text-white text-center" style="background-image: url('../uploads/office.jpg')">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="absolute top-0 left-0 right-0 p-3 md:p-4 bg-black bg-opacity-20 text-left text-sm z-20">
            <a href="../index.php" class="text-white hover:text-gray-300 transition">Latua Land</a> / Kantor Kami
        </div>
        <div class="relative z-10 max-w-6xl mx-auto px-5 animate-slide-in">
            <h1 class="text-3xl md:text-5xl font-bold text-white text-shadow-md">Kantor Kami</h1>
        </div>
    </div>

    <div class="py-12 px-5 bg-gray-100 text-center">
        <div class="max-w-sm mx-auto bg-white rounded-lg shadow-lg p-8">
            <img src="../uploads/latualogo.jpg" alt="Latuae Group Logo" class="max-w-[150px] h-auto mx-auto mb-5">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-3">Latuae Land</h2>
            <p class="text-base text-gray-600 leading-relaxed mb-2">Perjuangan<br>Ruko Golden No. 86,<br>Kelurahan Marga Mulya, Bekasi</p>
            <p class="text-base text-gray-600 mb-2">Email: <a href="mailto:bekasi.asiaone@gmail.com" class="text-blue-600 hover:text-blue-800 hover:underline transition">bekasi.asiaone@gmail.com</a></p>
            <p class="text-base text-gray-600 mb-4">Telp: <a href="tel:08111952667" class="text-blue-600 hover:text-blue-800 hover:underline transition">08111952667</a></p>
            <a href="#" class="inline-block px-5 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">Show Directions</a>
        </div>
    </div>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>