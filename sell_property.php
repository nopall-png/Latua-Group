<?php
// Tentukan email admin yang akan menerima pesan
$to_email = "latuealand@gmail.com"; // Ganti dengan alamat email admin Anda

// Sertakan koneksi database (hanya untuk proses backend jika diperlukan)
include 'includes/db_connect.php';

// Proses data jika ada permintaan AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax_submit'])) {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $alamat_email = trim($_POST['alamat_email'] ?? '');
    $nomor_handphone = trim($_POST['nomor_handphone'] ?? '');
    $metode_kontak = $_POST['metode_kontak'] ?? 'Email';
    $perihal = $_POST['perihal'] ?? '';
    $status_properti = $_POST['status_properti'] ?? '';
    $detail_properti = trim($_POST['detail_properti'] ?? '');

    // Validasi dasar
    if (empty($nama_lengkap) || empty($alamat_email) || empty($nomor_handphone) || empty($perihal) || empty($status_properti)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom yang ditandai * wajib diisi.']);
        exit();
    } elseif (!filter_var($alamat_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Format alamat email tidak valid.']);
        exit();
    } else {
        try {
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

            // Kirim email (opsional, hanya untuk notifikasi)
            mail($to_email, $subject, $email_content, $email_headers);

            echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim. Terimakasih']);
            exit();
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error menyimpan pengajuan: ' . $e->getMessage()]);
            exit();
        }
    }
}

// Sertakan header
include 'includes/header.php';
?>

<style>
    /* General Styling */
    body {
        background-color: #D3D3D3;
        font-family: Arial, sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        background-color: white;
        max-width: 700px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
        color: #5C2D91;
        text-transform: uppercase;
        font-size: 24px;
        text-align: center;
        margin-bottom: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    label {
        font-weight: bold;
        font-size: 16px;
    }
    label span {
        color: red;
    }
    input, select, textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 5px;
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
        gap: 20px;
        margin-top: 10px;
    }
    .radio-group input[type="radio"] {
        accent-color: #5C2D91;
    }
    .radio-group label {
        font-weight: normal;
        font-size: 14px;
        color: #333;
    }
    .btn-primary {
        display: inline-block;
        background-color: #FFC107;
        color: black;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        margin-top: 10px;
    }
    .btn-primary:hover {
        background-color: #e0a800;
    }
    button[type="submit"] {
        background-color: #FFC107;
        color: black;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        margin-top: 20px;
    }
    button[type="submit"]:hover {
        background-color: #e0a800;
    }
    .error-message {
        color: white;
        background-color: #DC3545;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
    }
    /* WhatsApp Chat Button */
    .whatsapp-chat {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #25D366;
        color: white;
        padding: 10px 15px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        text-decoration: none;
    }
    .whatsapp-chat i {
        font-size: 24px;
    }
    /* Footer Styling */
    footer {
        background-color: #5C2D91;
        color: white;
        text-align: center;
        padding: 20px 0;
        width: 100%;
        margin-top: 50px;
        position: relative;
        clear: both;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .form-group-inline {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <h2>Hubungi Kami</h2>
    <div id="message"></div>
    
    <form id="contact-form" class="contact-form">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap <span>*</span></label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
        </div>

        <div class="form-group-inline">
            <div class="form-group">
                <label for="alamat_email">Alamat Email <span>*</span></label>
                <input type="email" id="alamat_email" name="alamat_email" required>
            </div>
            <div class="form-group">
                <label for="nomor_handphone">Nomor Handphone / Telepon <span>*</span></label>
                <input type="tel" id="nomor_handphone" name="nomor_handphone" required>
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
                <option value="Mendaftarkan Properti">Mendaftarkan Properti</option>
                <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                <option value="Kritik & Saran">Kritik & Saran</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status_properti">Status Properti <span>*</span></label>
            <select id="status_properti" name="status_properti" required>
                <option value="">Please select</option>
                <option value="Dijual">Dijual</option>
                <option value="Disewakan">Disewakan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="detail_properti">Tulis detail informasi properti yang ingin Anda daftarkan</label>
            <textarea id="detail_properti" name="detail_properti" rows="5"></textarea>
        </div>
        
        <p>Atau isi detail informasi dari properti yang ingin Anda daftarkan melalui tautan di bawah ini:</p>
        <a href="/LatuaGroup/form-pendaftaran-properti.php" class="btn-primary">FORM PENDAFTARAN PROPERTI</a>
        
        <button type="submit">Kirim Pesan</button>
    </form>
</div>

<!-- WhatsApp Chat Button -->
<a href="https://wa.me/62123456789" class="whatsapp-chat">
    <i class="fab fa-whatsapp"></i> Butuh bantuan? Chat dengan kami
</a>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Mencegah reload halaman

    const formData = new FormData(this);
    formData.append('ajax_submit', true);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message); // Pop-up pesan sukses
            this.reset(); // Reset form setelah sukses
        } else {
            alert(data.message); // Pop-up pesan error
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error.message);
    });
});
</script>

<?php
// Sertakan footer sebagai elemen terpisah
include 'includes/footer.php';
?>