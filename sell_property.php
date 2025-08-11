<?php
// Tentukan email admin yang akan menerima pesan
$to_email = "latuealand@gmail.com"; // Ganti dengan alamat email admin Anda

// Sertakan koneksi database
include 'includes/db_connect.php';

// --- Perbaikan Utama di sini: Logika pemrosesan formulir ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak terduga.'];

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $alamat_email = trim($_POST['alamat_email'] ?? '');
    $nomor_handphone = trim($_POST['nomor_handphone'] ?? '');
    $metode_kontak = $_POST['metode_kontak'] ?? 'Email';
    $perihal = $_POST['perihal'] ?? '';
    $status_properti = $_POST['status_properti'] ?? '';
    $detail_properti = trim($_POST['detail_properti'] ?? '');

    // Validasi dasar
    if (empty($nama_lengkap) || empty($alamat_email) || empty($nomor_handphone) || empty($perihal) || empty($status_properti)) {
        $response['message'] = 'Semua kolom yang ditandai * wajib diisi.';
    } elseif (!filter_var($alamat_email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format alamat email tidak valid.';
    } else {
        try {
            // Persiapkan dan jalankan statement PDO untuk menyimpan data
            $stmt = $pdo->prepare("INSERT INTO pending_properties (user_name, user_email, user_phone, perihal, status_properti, detail_properti, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nama_lengkap, $alamat_email, $nomor_handphone, $perihal, $status_properti, $detail_properti]);

            // Buat subjek dan isi email
            $subject = "Pesan dari Formulir Hubungi Kami: " . $perihal;
            $email_content = "Nama Lengkap: " . $nama_lengkap . "\n";
            $email_content .= "Alamat Email: " . $alamat_email . "\n";
            $email_content .= "Nomor Handphone: " . $nomor_handphone . "\n";
            $email_content .= "Metode Kontak Pilihan: " . $metode_kontak . "\n";
            $email_content .= "Perihal: " . $perihal . "\n";
            $email_content .= "Status Properti: " . $status_properti . "\n";
            $email_content .= "Detail Properti: \n" . $detail_properti . "\n";

            // Atur header email
            $email_headers = "From: " . $nama_lengkap . " <" . $alamat_email . ">\r\n";
            $email_headers .= "Reply-To: " . $alamat_email . "\r\n";
            $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            // Kirim email
            if (mail($to_email, $subject, $email_content, $email_headers)) {
                $response = ['status' => 'success', 'message' => 'Formulir Anda telah berhasil dikirim! Tim kami akan segera menghubungi Anda.'];
            } else {
                $response = ['status' => 'success', 'message' => 'Formulir Anda telah berhasil dikirim ke database! Namun, email notifikasi gagal terkirim. Tim kami tetap akan memprosesnya.'];
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error menyimpan pengajuan ke database: ' . $e->getMessage();
            error_log("Database Error: " . $e->getMessage()); // Log error untuk debugging
        }
    }

    // Kirim respons JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Sertakan header
include 'includes/header.php';
?>

<style>
    /* General Styling */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }
    
    .container {
        background-color: #FFFFFF;
        max-width: 750px;
        width: 100%;
        margin: 20px auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    h2 {
        color: #2c3e50;
        text-transform: uppercase;
        font-size: 28px;
        text-align: center;
        margin-bottom: 25px;
        font-weight: 700;
    }
    
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }
    
    label {
        font-weight: 600;
        font-size: 16px;
        color: #444;
        display: block;
        margin-bottom: 8px;
    }
    
    label span {
        color: #e74c3c;
    }
    
    input, select, textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #E9ECEF;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    input:focus, select:focus, textarea:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        outline: none;
    }
    
    .form-group-inline {
        display: flex;
        gap: 20px;
    }
    
    .form-group-inline .form-group {
        flex: 1;
    }
    
    .radio-group {
        display: flex;
        gap: 15px;
        margin-top: 5px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .radio-group input[type="radio"] {
        margin: 0 5px 0 0;
        accent-color: #007bff;
    }
    
    .radio-group label {
        font-weight: normal;
        font-size: 15px;
        color: #555;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .btn-primary {
        display: inline-block;
        background-color: #007bff;
        color: #FFF;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top: 10px;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }
    
    button[type="submit"] {
        background-color: #28a745;
        color: #FFF;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 20px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    
    button[type="submit"]:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }
    
    #message {
        margin-bottom: 20px;
        text-align: center;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 16px;
        animation: slideIn 0.5s ease-out;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .alert-success {
        color: #155724;
        background-color: #D4EDDA;
        border: 1px solid #C3E6CB;
    }
    
    .alert-success::before {
        content: "✔"; /* Tanda centang */
        font-size: 18px;
        color: #155724;
    }
    
    .alert-danger {
        color: #721C24;
        background-color: #F8D7DA;
        border: 1px solid #F5C6CB;
    }
    
    .alert-danger::before {
        content: "✘"; /* Tanda silang */
        font-size: 18px;
        color: #721C24;
    }
    
    /* WhatsApp Chat Button */
    .whatsapp-chat {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(45deg, #10C65A, #0D994B);
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(16, 198, 90, 0.4);
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        z-index: 9999;
    }
    
    .whatsapp-chat i {
        font-size: 24px;
    }
    
    .whatsapp-chat:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(16, 198, 90, 0.6);
    }
    
    /* Footer Styling */
    footer {
        background-color: #2c3e50;
        color: white;
        text-align: center;
        padding: 15px 0;
        width: 100%;
        margin-top: 50px;
        position: relative;
        clear: both;
        font-size: 14px;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        body {
            min-height: auto;
        }
        
        .container {
            margin: 10px;
            padding: 20px;
        }
        
        h2 {
            font-size: 24px;
        }
        
        .form-group-inline {
            flex-direction: column;
            gap: 10px;
        }
        
        .whatsapp-chat {
            bottom: 10px;
            right: 10px;
            padding: 10px 15px;
            font-size: 14px;
        }
    }
</style>

<div class="container">
    <h2>Hubungi Kami</h2>
    <div id="message"></div>
    
    <form id="contact-form" class="contact-form">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap <span>*</span></label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan nama lengkap Anda">
        </div>

        <div class="form-group-inline">
            <div class="form-group">
                <label for="alamat_email">Alamat Email <span>*</span></label>
                <input type="email" id="alamat_email" name="alamat_email" required placeholder="contoh@email.com">
            </div>
            <div class="form-group">
                <label for="nomor_handphone">Nomor Handphone / Telepon <span>*</span></label>
                <input type="tel" id="nomor_handphone" name="nomor_handphone" required placeholder="081234567890">
            </div>
        </div>

        <div class="form-group">
            <label>Customer Service kami dapat menghubungi Anda melalui: <span>*</span></label>
            <div class="radio-group">
                <input type="radio" id="kontak_email" name="metode_kontak" value="Email" checked>
                <label for="kontak_email">Email</label>
                <input type="radio" id="kontak_telepon" name="metode_kontak" value="Handphone / Telepon">
                <label for="kontak_telepon">Handphone / Telepon</label>
                <input type="radio" id="kontak_keduanya" name="metode_kontak" value="Dapat keduanya">
                <label for="kontak_keduanya">Dapat keduanya</label>
            </div>
        </div>

        <div class="form-group">
            <label for="perihal">Perihal / Keperluan <span>*</span></label>
            <select id="perihal" name="perihal" required>
                <option value="">Pilih Perihal</option>
                <option value="Mendaftarkan Properti">Mendaftarkan Properti</option>
                <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                <option value="Kritik & Saran">Kritik & Saran</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status_properti">Status Properti <span>*</span></label>
            <select id="status_properti" name="status_properti" required>
                <option value="">Pilih Status</option>
                <option value="Dijual">Dijual</option>
                <option value="Disewakan">Disewakan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="detail_properti">Tulis detail informasi properti yang ingin Anda daftarkan</label>
            <textarea id="detail_properti" name="detail_properti" rows="5" placeholder="Masukkan detail properti (lokasi, ukuran, harga, dll.)"></textarea>
        </div>
        
        <p>Atau isi detail informasi dari properti yang ingin Anda daftarkan melalui tautan di bawah ini:</p>
        <a href="/LatuaGroup/form-pendaftaran-properti.php" class="btn-primary">FORM PENDAFTARAN PROPERTI</a>
        
        <button type="submit">Kirim Pesan</button>
    </form>
</div>

<a href="https://wa.me/62123456789" class="whatsapp-chat" target="_blank">
    <i class="fab fa-whatsapp"></i> Butuh bantuan?
</a>

<script>
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah reload halaman

        const formData = new FormData(this);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || 'Terjadi kesalahan pada server.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug: Cek data yang diterima
            const messageBox = document.getElementById('message');
            messageBox.innerHTML = ''; // Bersihkan pesan sebelumnya
            if (data.status === 'success') {
                messageBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                this.reset(); // Reset form setelah sukses
                // Tambahkan konfirmasi visual dengan delay sebelum menghilang
                setTimeout(() => {
                    messageBox.innerHTML = '<div class="alert alert-success">Pengajuan Anda telah diproses. Terima kasih!</div>';
                    setTimeout(() => {
                        messageBox.innerHTML = ''; // Hilangkan pesan setelah beberapa detik
                    }, 5000); // Hilang setelah 5 detik
                }, 3000); // Tunggu 3 detik sebelum ubah pesan
            } else {
                messageBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
            messageBox.scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error:', error); // Log error untuk debugging
            const messageBox = document.getElementById('message');
            messageBox.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan saat mengirim data. Silakan coba lagi atau hubungi dukungan.</div>`;
            messageBox.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

<?php
// Sertakan footer sebagai elemen terpisah
include 'includes/footer.php';
?>
