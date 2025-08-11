<?php include 'includes/header.php'; ?>

<!-- Bootstrap CSS from CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts for Poppins and Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-purple: #793475;
        --brand-purple-dark: #5a2658;
        --brand-green: #28a745; /* Warna hijau yang lebih standar */
        --brand-green-dark: #218838;
        --text-color-light: #fff;
        --text-color-dark: #333;
        --text-color-muted: #666;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: var(--text-color-dark);
    }
    
    .contact-page-wrapper {
        position: relative;
        width: 100%;
        min-height: 100vh;
        background-image: url('/Latua-Group/uploads/background.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0;
    }

    .hero-glass-effect {
        width: 100%;
        min-height: 100vh;
        background: rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(10.5px);
        -webkit-backdrop-filter: blur(10.5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        z-index: 5;
        padding: 50px 20px; /* Padding disesuaikan */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    /* MODAL STYLING */
    .modal-content-custom {
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        border: none;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .modal-title-custom {
        font-family: 'Inter', sans-serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-color-dark);
        margin-bottom: 20px;
    }
    
    .modal-body-custom {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .modal-contact-text {
        font-size: 1rem;
        color: var(--text-color-dark);
        margin-top: 15px;
    }
    
    .modal-button {
        width: 100%;
        max-width: 300px; /* Lebar maksimal tombol */
        padding: 12px 20px;
        border: none;
        border-radius: 50px; /* Bentuk kapsul */
        color: var(--text-color-light);
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .modal-button.btn-purple {
        background-color: var(--brand-purple);
    }
    .modal-button.btn-purple:hover {
        background-color: var(--brand-purple-dark);
    }

    .modal-button.btn-green {
        background-color: var(--brand-green);
    }
    .modal-button.btn-green:hover {
        background-color: var(--brand-green-dark);
    }

    .modal-button i {
        margin-right: 10px;
    }

    /* RESPONSIVITAS */
    @media (max-width: 768px) {
        .hero-title-contact {
            font-size: 2.5rem;
        }
        
        .hero-description-contact {
            font-size: 0.9rem;
            padding: 0 25px;
        }

        .modal-title-custom {
            font-size: 1.5rem;
        }
        
        .modal-contact-text {
            font-size: 0.9rem;
        }
    }
</style>

<div class="contact-page-wrapper">
    <div class="hero-section-contact">
        <div class="hero-glass-effect">
            <div class="hero-content-contact text-center">
                <h1 class="hero-title-contact">Hubungi Kami</h1>
                <div class="hero-separator"></div>
                <p class="hero-description-contact">
                    Kami siap membantu Anda. Silakan pilih salah satu metode di bawah ini untuk menghubungi tim kami.
                </p>
                <button type="button" class="btn btn-hubungi-kami" data-bs-toggle="modal" data-bs-target="#contactModal">
                    HUBUNGI KAMI
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Kontak -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-custom">
                <h5 class="modal-title-custom" id="contactModalLabel">Hubungi kami di:</h5>
                <a href="tel:0214705662" class="modal-button btn-purple">
                    <i class="fas fa-phone"></i> 021 470 5662
                </a>
                <a href="tel:08111952667" class="modal-button btn-purple">
                    <i class="fas fa-phone"></i> 0811 1952 667
                </a>

                <p class="modal-contact-text">atau kontak kami via email</p>
                <a href="mailto:bekasi.asiaone@gmail.com" class="modal-button btn-purple">
                    <i class="fas fa-envelope"></i> Hubungi Kami
                </a>

                <p class="modal-contact-text">atau chat dengan kami via whatsapp</p>
                <a href="https://wa.me/628170888820" class="modal-button btn-green">
                    <i class="fab fa-whatsapp"></i> Hubungi Kami
                </a>
                <span style="font-size: 14px; color: #4CAF50; font-weight: bold; margin-top: 5px;">Online</span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk menampilkan modal secara otomatis saat halaman dimuat -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('contactModal'));
        myModal.show();
    });
</script>
