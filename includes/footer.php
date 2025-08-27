<?php
// Dapatkan waktu saat ini (WIB)
date_default_timezone_set('Asia/Jakarta');
$currentTime = date('H:i');
$currentDay = date('l');
$currentDate = date('d F Y');

// Tentukan jam operasional
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

<footer class="bg-white text-black pt-12 pb-6 border-t border-gray-200">
  <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10 text-center md:text-left">
    
    <!-- Kolom 1 - Menu -->
    <div>
      <h4 class="text-lg font-semibold mb-4">Menu</h4>
      <ul class="space-y-2 text-sm text-gray-600">
        <li><a href="#" class="hover:text-black transition">Cari Properti</a></li>
        <li><a href="#" class="hover:text-black transition">Cari Agen</a></li>
        <li><a href="#" class="hover:text-black transition">Pasarkan Properti</a></li>
      </ul>
    </div>

    <!-- Kolom 2 - Logo Tengah -->
    <div class="flex flex-col items-center justify-center">
      <h2 class="text-3xl font-bold tracking-wide">LATUEA LAND</h2>
      <p class="mt-2 text-sm text-gray-500">Membangun Hunian & Investasi Terpercaya</p>
      <div class="flex space-x-4 mt-4">
        <a href="https://www.instagram.com/latuealand/?igsh=OWRhYTd6am42cjly&utm_source=qr" class="text-2xl text-black hover:opacity-60 transition">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" class="text-2xl text-black hover:opacity-60 transition">
          <i class="fab fa-facebook"></i>
        </a>
        <a href="#" class="text-2xl text-black hover:opacity-60 transition">
          <i class="fab fa-twitter"></i>
        </a>
      </div>
    </div>

    <!-- Kolom 3 - Jam Operasional -->
    <div>
      <h4 class="text-lg font-semibold mb-4">Jam Operasional</h4>
      <p class="text-sm text-gray-600">Senin - Jumat: <span class="font-bold text-black">09:00 - 17:00</span></p>
      <p class="text-sm text-gray-600">Sabtu: <span class="font-bold text-black">09:00 - 14:00</span></p>
      <p class="text-sm text-gray-600">Minggu & Hari Libur: <span class="font-bold text-black">Tutup</span></p>
      <p class="mt-3 text-sm font-bold <?php echo $isOpen ? 'text-green-600' : 'text-red-600'; ?>">
        <?php echo $isOpen ? 'Sedang Buka Sekarang' : 'Tutup Sekarang'; ?>
      </p>
    </div>

  </div>

  <!-- Copyright -->
  <div class="border-t border-gray-300 mt-10 pt-4 text-center text-xs text-gray-500">
    © 2025 Latuae Group. All rights reserved.
  </div>
</footer>
