<?php include '../includes/header.php'; ?>

<!-- Konten Halaman FAQ & Kontak -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    .accordion-button.active {
        color: #3B82F6 !important;
    }
    /* Tambahkan transisi halus untuk modal */
    .modal-container {
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }
    .modal-content-wrapper {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    }
</style>

<div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 bg-cover bg-center" style="background-image: url('https://placehold.co//E2E8F0/1F2937');">
    <!-- Glass Effect Overlay -->
    <div class="w-full min-h-screen bg-white bg-opacity-70 backdrop-blur-md flex flex-col justify-center items-center p-6 md:p-12">
        <!-- Konten Utama -->
        <div class="max-w-4xl mx-auto text-center space-y-6">
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 drop-shadow-md">Pertanyaan yang Sering Diajukan</h1>
            <p class="text-sm md:text-lg text-gray-700 max-w-2xl mx-auto">Temukan jawaban atas pertanyaan umum tentang layanan kami, proses pembelian, penyewaan, penjualan properti, dan lainnya.</p>

            <!-- Accordion -->
            <div class="w-full space-y-4 text-left" id="faq-accordion">

                <!-- Reusable accordion item (copy untuk setiap kategori) -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <button type="button" class="accordion-button w-full text-left p-4 md:p-6 font-semibold text-lg md:text-xl text-gray-900 focus:outline-none flex justify-between items-center transition-all duration-300 hover:bg-blue-600 hover:text-white" aria-expanded="false" aria-controls="collapse-pembeli" data-target="collapse-pembeli">
                        <span>Untuk Pembeli</span>
                        <i class="fa-solid fa-plus ml-2 transition-all duration-300"></i>
                    </button>
                    <div id="collapse-pembeli" class="accordion-content max-h-0 overflow-hidden border-t border-gray-200" aria-hidden="true">
                        <div class="p-4 md:p-6 text-sm md:text-base text-gray-700 space-y-3">
                            <p><strong>Apa yang ditawarkan situs ini?</strong><br>Kami membantu menemukan, membeli, menjual, atau menyewa properti (rumah, apartemen, ruko, tanah) serta menyediakan layanan konsultasi dan pemasaran properti.</p>
                            <p><strong>Bagaimana cara mencari properti?</strong><br>Gunakan fitur pencarian dengan filter (lokasi, harga, tipe properti, luas, jumlah kamar). Kamu juga bisa menyimpan pencarian untuk update otomatis.</p>
                            <p><strong>Apakah pendaftaran wajib untuk melihat listing?</strong><br>Tidak wajib, tetapi mendaftar memberi akses fitur tambahan seperti menyimpan favorit, menerima pemberitahuan, dan menghubungi agen langsung.</p>
                            <p><strong>Bagaimana proses membeli properti lewat situs ini?</strong><br>Pilih properti → hubungi agen/penjual → kunjungan/virtual tour → negosiasi → tanda tangan perjanjian → proses pembayaran & balik nama.</p>
                            <p><strong>Apakah ada jaminan kualitas listing?</strong><br>Kami melakukan verifikasi dasar (kontak penjual/agen dan data properti). Untuk pemeriksaan mendalam, kami sarankan inspeksi independen.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <button type="button" class="accordion-button w-full text-left p-4 md:p-6 font-semibold text-lg md:text-xl text-gray-900 focus:outline-none flex justify-between items-center transition-all duration-300 hover:bg-blue-600 hover:text-white" aria-expanded="false" aria-controls="collapse-penyewa" data-target="collapse-penyewa">
                        <span>Untuk Penyewa & Pemilik Sewa</span>
                        <i class="fa-solid fa-plus ml-2 transition-all duration-300"></i>
                    </button>
                    <div id="collapse-penyewa" class="accordion-content max-h-0 overflow-hidden border-t border-gray-200" aria-hidden="true">
                        <div class="p-4 md:p-6 text-sm md:text-base text-gray-700 space-y-3">
                            <p><strong>Bagaimana cara memasang iklan sewa?</strong><br>Buat akun → pilih paket listing → unggah foto & deskripsi → terbitkan. Kami juga menawarkan opsi promosi berbayar.</p>
                            <p><strong>Bagaimana aturan pembatalan atau pengembalian deposit?</strong><br>Aturan bergantung pada perjanjian sewa yang disepakati. Kami sarankan menuliskan ketentuan deposit di kontrak sewa.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <button type="button" class="accordion-button w-full text-left p-4 md:p-6 font-semibold text-lg md:text-xl text-gray-900 focus:outline-none flex justify-between items-center transition-all duration-300 hover:bg-blue-600 hover:text-white" aria-expanded="false" aria-controls="collapse-penjual" data-target="collapse-penjual">
                        <span>Untuk Penjual/Agen</span>
                        <i class="fa-solid fa-plus ml-2 transition-all duration-300"></i>
                    </button>
                    <div id="collapse-penjual" class="accordion-content max-h-0 overflow-hidden border-t border-gray-200" aria-hidden="true">
                        <div class="p-4 md:p-6 text-sm md:text-base text-gray-700 space-y-3">
                            <p><strong>Bagaimana cara memasang listing jual?</strong><br>Daftar akun agen/penjual → pilih paket → isi detail properti → unggah foto/sertifikat → publikasi.</p>
                            <p><strong>Berapa biaya untuk memasang listing?</strong><br>Tersedia paket gratis dan berbayar. Detail harga dan fitur ada di halaman Paket & Harga.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <button type="button" class="accordion-button w-full text-left p-4 md:p-6 font-semibold text-lg md:text-xl text-gray-900 focus:outline-none flex justify-between items-center transition-all duration-300 hover:bg-blue-600 hover:text-white" aria-expanded="false" aria-controls="collapse-pembiayaan" data-target="collapse-pembiayaan">
                        <span>Pembiayaan</span>
                        <i class="fa-solid fa-plus ml-2 transition-all duration-300"></i>
                    </button>
                    <div id="collapse-pembiayaan" class="accordion-content max-h-0 overflow-hidden border-t border-gray-200" aria-hidden="true">
                        <div class="p-4 md:p-6 text-sm md:text-base text-gray-700 space-y-3">
                            <p><strong>Apakah ada informasi KPR di situs ini?</strong><br>Kami menyediakan panduan KPR dan bisa membantu menghubungkan ke mitra bank atau broker KPR.</p>
                            <p><strong>Bisakah menghitung cicilan KPR?</strong><br>Ada kalkulator KPR di situs untuk estimasi cicilan berdasarkan uang muka, tenor, dan suku bunga.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <button type="button" class="accordion-button w-full text-left p-4 md:p-6 font-semibold text-lg md:text-xl text-gray-900 focus:outline-none flex justify-between items-center transition-all duration-300 hover:bg-blue-600 hover:text-white" aria-expanded="false" aria-controls="collapse-legal" data-target="collapse-legal">
                        <span>Legal</span>
                        <i class="fa-solid fa-plus ml-2 transition-all duration-300"></i>
                    </button>
                    <div id="collapse-legal" class="accordion-content max-h-0 overflow-hidden border-t border-gray-200" aria-hidden="true">
                        <div class="p-4 md:p-6 text-sm md:text-base text-gray-700 space-y-3">
                            <p><strong>Dokumen apa yang perlu disiapkan saat jual/beli?</strong><br>Sertifikat tanah, IMB, PBB, KTP pemilik, bukti pembayaran pajak, dan dokumen pendukung lainnya.</p>
                            <p><strong>Apakah kami membantu urusan balik nama?</strong><br>Kami dapat merekomendasikan notaris/PPAT untuk proses balik nama (layanan ini biasanya berbayar).</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tombol "Hubungi Kami Sekarang" -->
            <div class="text-center">
                <button id="contact-button" class="mt-8 px-8 py-3 bg-green-500 text-white font-semibold rounded-full shadow-lg transition-all duration-300 hover:bg-blue-600 hover:text-white transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Hubungi Kami Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kontak -->
<div id="contact-modal" class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 invisible opacity-0 modal-container flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full relative transform scale-95 opacity-0 transition-all duration-300" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title">
        <button id="modal-close" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-all duration-300 focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
        <h3 id="contact-modal-title" class="text-2xl font-bold text-center mb-4">Hubungi Kami</h3>
        <p class="text-gray-700 text-center mb-4">Pilih salah satu cara berikut untuk terhubung dengan kami:</p>
        <div class="flex flex-col space-y-3">
            <a href="https://wa.me/628111440205" class="w-full text-center py-3 bg-green-500 text-white font-semibold rounded-full shadow-md hover:bg-green-600 transition-all duration-300">
                <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
            </a>
            <a href="tel:0214705662" class="w-full text-center py-3 bg-blue-600 text-white font-semibold rounded-full shadow-md hover:bg-blue-700 transition-all duration-300">
                <i class="fas fa-phone mr-2"></i> Telepon Kami
            </a>
            <a href="mailto:latuealand@gmail.com" class="w-full text-center py-3 bg-red-600 text-white font-semibold rounded-full shadow-md hover:bg-red-700 transition-all duration-300">
                <i class="fas fa-envelope mr-2"></i> Kirim Email
            </a>
        </div>
    </div>
</div>

<script>
    // ---- Perilaku Accordion (mudah diakses) ----
    const accordionButtons = document.querySelectorAll('.accordion-button');

    function closeAllAccordions() {
        document.querySelectorAll('.accordion-content').forEach(content => {
            content.style.maxHeight = null;
            content.setAttribute('aria-hidden', 'true');
            const btn = document.querySelector('[data-target="' + content.id + '"]');
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
                const icon = btn.querySelector('i');
                if (icon) { icon.classList.remove('fa-minus'); icon.classList.add('fa-plus'); }
            }
        });
    }

    accordionButtons.forEach(btn => {
        const targetId = btn.getAttribute('data-target');
        const panel = document.getElementById(targetId);

        // Dukungan keyboard
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
        });

        btn.addEventListener('click', () => {
            // Jika panel sudah terbuka -> tutup
            const isOpen = btn.getAttribute('aria-expanded') === 'true';

            if (isOpen) {
                panel.style.maxHeight = null;
                panel.setAttribute('aria-hidden', 'true');
                btn.setAttribute('aria-expanded', 'false');
                btn.querySelector('i').classList.remove('fa-minus');
                btn.querySelector('i').classList.add('fa-plus');
            } else {
                // Tutup panel lain terlebih dahulu
                closeAllAccordions();

                panel.style.maxHeight = panel.scrollHeight + 'px';
                panel.setAttribute('aria-hidden', 'false');
                btn.setAttribute('aria-expanded', 'true');
                btn.querySelector('i').classList.remove('fa-plus');
                btn.querySelector('i').classList.add('fa-minus');
            }
        });
    });

    // ---- Perilaku Modal (dengan transisi halus) ----
    const contactButton = document.getElementById('contact-button');
    const contactModal = document.getElementById('contact-modal');
    const modalContent = contactModal.querySelector('div:first-of-type'); // Mengambil div pertama di dalam modal
    const modalClose = document.getElementById('modal-close');

    function showModal() {
        contactModal.classList.remove('hidden');
        contactModal.classList.add('flex');
        
        // Memaksa browser untuk menghitung ulang style untuk transisi
        void contactModal.offsetWidth;

        contactModal.classList.remove('invisible', 'opacity-0');
        contactModal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');

        document.body.style.overflow = 'hidden'; // Hentikan scroll belakang layar
        
        // Fokus ke tombol tutup atau link pertama untuk aksesibilitas
        const firstFocusable = contactModal.querySelector('a') || modalClose;
        if (firstFocusable) firstFocusable.focus();
    }

    function hideModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        // Tunggu transisi selesai sebelum menyembunyikan sepenuhnya
        setTimeout(() => {
            contactModal.classList.remove('opacity-100');
            contactModal.classList.add('invisible', 'opacity-0');
            document.body.style.overflow = ''; // Kembalikan scroll
            contactButton.focus(); // Fokus kembali ke tombol "Hubungi Kami"
        }, 300);
    }

    // Pastikan tombol ada sebelum menambahkan event listener
    if (contactButton) {
        contactButton.addEventListener('click', showModal);
    }
    if (modalClose) {
        modalClose.addEventListener('click', hideModal);
    }

    // Klik di luar dialog untuk menutup
    contactModal.addEventListener('click', (e) => {
        if (e.target === contactModal) {
            hideModal();
        }
    });

    // Tutup dengan tombol ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !contactModal.classList.contains('invisible')) {
            hideModal();
        }
    });

    // Pastikan semua panel tertutup saat load
    window.addEventListener('load', () => {
        closeAllAccordions();
    });
</script>

<?php include '../includes/footer.php'; ?>
