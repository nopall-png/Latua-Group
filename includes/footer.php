<?php
// Dapatkan waktu saat ini (WIB)
date_default_timezone_set('Asia/Jakarta');
$currentTime = date('H:i');
$currentDay = date('l'); // Hari dalam bahasa Inggris (e.g., Thursday)
$currentDate = date('d F Y'); // Tanggal lengkap (e.g., 07 August 2025)

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

<footer class="asiaone-footer">
    <div class="footer-container">
        <div class="footer-section">
            <a href="/LatuaGroup/agen.php" class="footer-button">
                <i class="fas fa-users"></i>
                <span>CARI AGEN</span>
                <p>"Agen-agen kami akan membantu menemukan properti idaman anda"</p>
            </a>
        </div>

        <div class="footer-section">
            <a href="#" class="footer-button">
                <i class="fas fa-home"></i>
                <span>CARI PROPERTI</span>
                <p>Cari Rumah, Apartemen, Ruko/Komersil, Tanah, atau Gudang</p>
            </a>
        </div>

        <div class="footer-section">
            <a href="/LatuaGroup/sell_property.php" class="footer-button sell-rent-button">
                <i class="fas fa-key"></i>
                <span>INGIN MENJUAL / MENYEWAKAN?</span>
                <p>ISI FORM ></p>
            </a>
        </div>

        <div class="footer-section jam-operasional">
            <p>JAM OPERASIONAL</p>
            <p>Senin - Jumat: <span class="time">09:00 - 17:00</span></p>
            <p>Sabtu: <span class="time">09:00 - 12:00</span></p>
            <p>Minggu & Hari Libur: <span class="time">Tutup</span></p>
            <?php if ($isOpen): ?>
                <p style="color: green; font-weight: bold;">Sedang Buka Sekarang</p>
            <?php else: ?>
                <p style="color: red; font-weight: bold;">Tutup Sekarang</p>
            <?php endif; ?>
        </div>

        <div class="footer-section">
            <p>CALL CENTER</p>
            <a href="tel:+6214705862" class="call-center-button">
                <i class="fas fa-phone"></i> 021 470 5862
            </a>
            <a href="https://wa.me/6214705862" class="whatsapp-button">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
    <div class="copyright">
        <p>© 2025 Property Web. All rights reserved.</p>
    </div>
</footer>

<style>
    /* CSS untuk footer */
    .asiaone-footer {
        background-color: #EDEDED;
        padding: 30px 0;
        color: #333;
        font-family: 'Arial', sans-serif;
        border-top: 1px solid #DDD;
    }

    .footer-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        flex-wrap: wrap;
    }

    .footer-section {
        flex: 1;
        min-width: 180px;
        text-align: center;
    }

    .footer-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #00C4CC;
        color: white;
        padding: 15px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .footer-button:hover {
        background-color: #009DA6;
    }

    .footer-button i {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .footer-button span {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .footer-button p {
        font-size: 12px;
        margin: 0;
        color: #FFF;
    }

    .sell-rent-button {
        background-color: #00C4CC;
    }

    .call-center-button {
        background-color: #FFC107;
        padding: 15px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        border-radius: 5px;
        display: inline-block;
        transition: background-color 0.3s;
        margin-top: 5px;
    }

    .call-center-button:hover {
        background-color: #FFA000;
    }

    .whatsapp-button {
        background-color: #4CAF50;
        padding: 15px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        border-radius: 5px;
        display: inline-block;
        margin-top: 5px;
        transition: background-color 0.3s;
    }

    .whatsapp-button:hover {
        background-color: #45A049;
    }

    .jam-operasional {
        background-color: transparent;
        padding: 15px;
    }

    .jam-operasional p {
        margin: 5px 0;
        font-size: 14px;
        color: #666;
    }

    .jam-operasional .time {
        font-weight: bold;
        color: #333;
    }

    .copyright {
        margin-top: 20px;
        font-size: 12px;
        color: #666;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            text-align: center;
        }

        .footer-button, .call-center-button, .whatsapp-button {
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>