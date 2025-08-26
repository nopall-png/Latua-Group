<?php
// Dapatkan waktu saat ini (WIB)
date_default_timezone_set('Asia/Jakarta');
$currentTime = date('H:i');
$currentDay = date('l'); // Hari dalam bahasa Inggris (e.g., Tuesday)
$currentDate = date('d F Y'); // Tanggal lengkap (e.g., 19 August 2025)

// Tentukan status operasional
$operatingHours = [
    'Monday' => ['09:00', '17:00'],
    'Tuesday' => ['09:00', '17:00'],
    'Wednesday' => ['09:00', '17:00'],
    'Thursday' => ['09:00', '17:00'],
    'Friday' => ['09:00', '17:00'],
    'Saturday' => ['09:00', '14:00'],
    'Sunday' => ['Tutup', 'Tutup']
];

$hours = $operatingHours[$currentDay];
$isOpen = false;

if ($hours[0] !== 'Tutup') {
    $openTime = strtotime($hours[0]);
    $closeTime = strtotime($hours[1]);
    $currentTimeStamp = strtotime($currentTime);

    $isOpen = ($currentTimeStamp >= $openTime && $currentTimeStamp < $closeTime);
}
?>

<footer class="bg-white text-black py-6 border-t border-gray-300">
    <div class="container mx-auto px-4 flex flex-wrap justify-between items-center gap-6">
        <!-- Left Section (Menu) -->
        <div class="flex-1 min-w-[150px] text-center md:text-left">
            <p class="text-sm font-raleway">Menu</p>
            <p class="text-sm font-raleway">Cari Properti</p>
            <p class="text-sm font-raleway">Cari Agen</p>
            <p class="text-sm font-raleway">Pasarkan Properti</p>
        </div>

        <!-- Center Section (Logo) -->
        <div class="flex-1 min-w-[150px] text-center">
            <h2 class="text-3xl font-raleway font-bold">LATUEA LAND</h2>
        </div>

        <!-- Right Section (Jam Operasional & Social Media) -->
        <div class="flex-1 min-w-[150px] text-center md:text-right">
            <p class="text-sm font-raleway font-bold">JAM OPERASIONAL</p>
            <p class="text-sm font-raleway">Senin - Jumat: <span class="font-bold">09:00 - 17:00</span></p>
            <p class="text-sm font-raleway">Sabtu: <span class="font-bold">09:00 - 14:00</span></p>
            <p class="text-sm font-raleway">Minggu & Hari Libur: <span class="font-bold">Tutup</span></p>
            <p class="text-sm font-raleway <?php echo $isOpen ? 'text-green-600' : 'text-red-600'; ?> font-bold">
                <?php echo $isOpen ? 'Sedang Buka Sekarang' : 'Tutup Sekarang'; ?>
            </p>
            <div class="mt-2">
                <a href="https://www.instagram.com/latuealand/?igsh=OWRhYTd6am42cjly&utm_source=qr" class="text-2xl hover:opacity-70 transition">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <p class="text-xs font-raleway">© 2025 Latuae Group. All rights reserved.</p>
    </div>
</footer>