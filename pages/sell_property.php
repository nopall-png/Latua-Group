<?php
// Set dynamic title for this page
$page_title = 'Hubungi Kami';

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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(1.25rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-1.25rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            opacity: 0;
            transform: translateY(1.25rem);
            animation: fadeIn 0.5s ease-in-out forwards;
        }
        .animate-slide-in {
            opacity: 0;
            transform: translateY(-1.25rem);
            animation: slideIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="font-lato bg-gray-100 text-gray-800 flex flex-col items-center min-h-screen">
    <!-- Header sudah full-width dari header.php -->
    <div class="max-w-3xl w-full mx-auto p-8 bg-white rounded-xl shadow-lg mt-16 animate-fade-in"> <!-- Tambah mt-16 untuk jarak dari header -->
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 uppercase text-center mb-6">Hubungi Kami</h2>
        <div id="message" class="mb-5 text-center"></div>   
        
        <form id="contact-form" class="contact-form">
            <div class="mb-5">
                <label for="nama_lengkap" class="block text-base font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-600">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan nama lengkap Anda" class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition">
            </div>

            <div class="flex flex-col md:flex-row gap-5 mb-5">
                <div class="flex-1">
                    <label for="alamat_email" class="block text-base font-semibold text-gray-700 mb-2">Alamat Email <span class="text-red-600">*</span></label>
                    <input type="email" id="alamat_email" name="alamat_email" required placeholder="contoh@email.com" class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition">
                </div>
                <div class="flex-1">
                    <label for="nomor_handphone" class="block text-base font-semibold text-gray-700 mb-2">Nomor Handphone / Telepon <span class="text-red-600">*</span></label>
                    <input type="tel" id="nomor_handphone" name="nomor_handphone" required placeholder="081234567890" class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition">
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-base font-semibold text-gray-700 mb-2">Customer Service kami dapat menghubungi Anda melalui: <span class="text-red-600">*</span></label>
                <div class="flex flex-wrap gap-4 items-center">
                    <input type="radio" id="kontak_email" name="metode_kontak" value="Email" checked class="text-blue-600">
                    <label for="kontak_email" class="text-sm text-gray-600 flex items-center">Email</label>
                    <input type="radio" id="kontak_telepon" name="metode_kontak" value="Handphone / Telepon" class="text-blue-600">
                    <label for="kontak_telepon" class="text-sm text-gray-600 flex items-center">Handphone / Telepon</label>
                    <input type="radio" id="kontak_keduanya" name="metode_kontak" value="Dapat keduanya" class="text-blue-600">
                    <label for="kontak_keduanya" class="text-sm text-gray-600 flex items-center">Dapat keduanya</label>
                </div>
            </div>

            <div class="mb-5">
                <label for="perihal" class="block text-base font-semibold text-gray-700 mb-2">Perihal / Keperluan <span class="text-red-600">*</span></label>
                <select id="perihal" name="perihal" required class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition">
                    <option value="">Pilih Perihal</option>
                    <option value="Mendaftarkan Properti">Mendaftarkan Properti</option>
                    <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                    <option value="Kritik & Saran">Kritik & Saran</option>
                </select>
            </div>

            <div class="mb-5">
                <label for="status_properti" class="block text-base font-semibold text-gray-700 mb-2">Status Properti <span class="text-red-600">*</span></label>
                <select id="status_properti" name="status_properti" required class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition">
                    <option value="">Pilih Status</option>
                    <option value="Dijual">Dijual</option>
                    <option value="Disewakan">Disewakan</option>
                </select>
            </div>

            <div class="mb-5">
                <label for="detail_properti" class="block text-base font-semibold text-gray-700 mb-2">Tulis detail informasi properti yang ingin Anda daftarkan</label>
                <textarea id="detail_properti" name="detail_properti" rows="5" placeholder="Masukkan detail properti (lokasi, ukuran, harga, dll.)" class="w-full p-3 border border-gray-200 rounded-lg text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none transition"></textarea>
            </div>

            <p class="text-base text-gray-600 mb-4">Atau isi detail informasi dari properti yang ingin Anda daftarkan melalui tautan di bawah ini:</p>
            <a href="../sell_property.php" class="inline-block bg-blue-600 text-white rounded-lg font-semibold px-5 py-3 hover:bg-blue-700 hover:-translate-y-0.5 transition">FORM PENDAFTARAN PROPERTI</a>

            <button type="submit" class="w-full bg-green-500 text-white rounded-lg font-semibold px-5 py-3 mt-5 hover:bg-green-600 hover:-translate-y-0.5 transition">Kirim Pesan</button>
        </form>
    </div>

    <a href="https://wa.me/+628111952667" class="fixed bottom-4 right-4 bg-gradient-to-r from-green-500 to-green-600 text-white p-3 rounded-full flex items-center gap-2 shadow-lg hover:shadow-xl hover:-translate-y-1 transition z-50" target="_blank">
        <i class="fab fa-whatsapp text-xl"></i> Butuh bantuan?
    </a>

    <script>
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('../process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Status:', response.status);
                console.log('Headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Response text:', text);
                        throw new Error(text || 'Terjadi kesalahan pada server.');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                const messageBox = document.getElementById('message');
                messageBox.innerHTML = '';
                if (data.status === 'success') {
                    messageBox.innerHTML = `<div class="p-4 rounded-lg flex items-center gap-2 text-green-800 bg-green-200 animate-slide-in">✔ ${data.message}</div>`;
                    this.reset();
                    setTimeout(() => {
                        messageBox.innerHTML = `<div class="p-4 rounded-lg flex items-center gap-2 text-green-800 bg-green-200 animate-slide-in">Pengajuan Anda telah diproses. Terima kasih!</div>`;
                        setTimeout(() => {
                            messageBox.innerHTML = '';
                        }, 5000);
                    }, 3000);
                } else {
                    messageBox.innerHTML = `<div class="p-4 rounded-lg flex items-center gap-2 text-red-800 bg-red-200 animate-slide-in">✘ ${data.message}</div>`;
                }
                messageBox.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                const messageBox = document.getElementById('message');
                messageBox.innerHTML = `<div class="p-4 rounded-lg flex items-center gap-2 text-red-800 bg-red-200 animate-slide-in">✘ Terjadi kesalahan saat mengirim data. Silakan coba lagi atau hubungi dukungan.</div>`;
                messageBox.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>