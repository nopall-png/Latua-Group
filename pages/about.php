<?php
require __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Latuae Land</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
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
    <div class="relative w-full h-[70vh] min-h-[400px] bg-cover bg-center flex items-center justify-center text-white text-center" style="background-image: url('./Uploads/about_us.jpg')">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="absolute top-0 left-0 right-0 p-4 md:p-6 bg-black bg-opacity-30 text-left text-sm z-20">
            <a href="./pages/index.php" class="text-white hover:text-gray-300 transition">Latua Land</a> / Tentang Kami
        </div>
        <div class="relative z-10 max-w-6xl mx-auto px-5 animate-slide-in">
            <h1 class="text-3xl md:text-5xl font-bold text-white text-shadow-md">Tentang Kami</h1>
        </div>
    </div>

    <div class="py-16 px-5 bg-white">
        <div class="max-w-4xl mx-auto p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-5 text-center">Selamat Datang di Latuae Land</h2>
            <p class="subtitle text-base md:text-lg italic text-gray-800 mb-6 text-center">Solusi Properti Cerdas, Aman, dan Menguntungkan</p>
            <p class="text-base text-gray-800 leading-relaxed mb-5 text-justify md:max-w-2xl md:mx-auto">Di Latuae Land, kami memahami bahwa setiap properti bukan sekadar bangunan—tetapi impian, tujuan, dan investasi masa depan.</p>
            <p class="text-base text-gray-800 leading-relaxed mb-5 text-justify md:max-w-2xl md:mx-auto">Sebagai perusahaan jasa properti yang berpengalaman dan terpercaya, kami hadir untuk membantu Anda dalam proses jual, sewa, maupun beli properti dengan cara yang mudah, cepat, dan menguntungkan.</p>
            
            <div class="flex flex-col md:flex-row gap-6 md:gap-10 mt-10">
                <div class="flex-1 text-center">
                    <h3 class="text-xl md:text-2xl font-semibold text-blue-800 mb-4">Misi Kami</h3>
                    <p class="text-base text-gray-800">Memberikan solusi properti terbaik dengan integritas tinggi, transparansi, dan pelayanan prima untuk mewujudkan impian properti setiap klien.</p>
                </div>
                <div class="flex-1 text-center">
                    <h3 class="text-xl md:text-2xl font-semibold text-blue-800 mb-4">Visi Kami</h3>
                    <p class="text-base text-gray-800">Menjadi perusahaan properti terdepan yang diakui sebagai mitra terpercaya dalam menciptakan nilai tambah bagi semua pihak.</p>
                </div>
            </div>

            <h3 class="text-xl md:text-2xl font-semibold text-blue-800 mt-10 mb-5 text-center">Mengapa Memilih Kami</h3>
            <div class="flex flex-col md:flex-row gap-5 md:gap-10 mt-5 justify-center">
                <div class="flex-1 max-w-xs mx-auto">
                    <ul class="list-none p-0 m-0 text-left">
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Pelayanan profesional dan ramah</li>
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Jaringan luas di pasar properti</li>
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Proses transparan dan aman</li>
                    </ul>
                </div>
                <div class="flex-1 max-w-xs mx-auto">
                    <ul class="list-none p-0 m-0 text-left">
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Konsultasi gratis untuk klien</li>
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Solusi disesuaikan dengan kebutuhan</li>
                        <li class="mb-2 pl-6 relative before:content-['\2022'] before:text-blue-800 before:font-bold before:absolute before:left-0">Pengalaman bertahun-tahun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>