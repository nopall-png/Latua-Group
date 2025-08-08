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
        --brand-green: #4CAF50;
        --brand-green-dark: #45A049;
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
        padding-top: 50px;
        padding-bottom: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .hero-content-contact {
        position: relative;
        z-index: 10;
        color: var(--text-color-light);
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
    }

    .hero-title-contact {
        font-family: 'Inter', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--text-color-light);
        margin-bottom: 5px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .hero-separator {
        width: 60px;
        height: 4px;
        background-color: var(--text-color-light);
        margin: 10px auto 20px auto;
        border-radius: 2px;
    }

    .hero-description-contact {
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 40px;
    }

    .btn-hubungi-kami {
        padding: 15px 30px;
        background-color: var(--brand-purple);
        color: var(--text-color-light);
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: background-color 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .btn-hubungi-kami:hover {
        background-color: var(--brand-purple-dark);
    }
    
    /* --- MODAL STYLING --- */
    .modal-content-custom {
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        border: none;
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
    }
    
    .modal-button {
        width: 100%;
        max-width: 250px;
        padding: 12px 20px;
        border: none;
        border-radius: 25px;
        color: var(--text-color-light);
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .modal-button:hover {
        transform: translateY(-1px);
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

    /* --- RESPONSIVITAS --- */
    @media (max-width: 768px) {
        .hero-title-contact {
            font-size: 2.5rem;
        }
        
        .hero-glass-effect {
            padding: 30px 15px;
        }

        .hero-description-contact {
            font-size: 0.9rem;
            padding: 0 25px;
        }
    }
</style>

<div class="contact-page-wrapper">
    <div class="hero-section-contact">
        <div class="hero-glass-effect">
            <!-- Menghilangkan hero-content dan button -->
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
                <a href="tel:0214705662" class="modal-button btn-purple d-flex justify-content-center align-items-center">
                    <i class="fas fa-phone"></i> 021 470 5662
                </a>
                <a href="tel:08111952667" class="modal-button btn-purple d-flex justify-content-center align-items-center">
                    <i class="fas fa-phone"></i> 0811 1952 667
                </a>

                <p class="modal-contact-text">atau kontak kami via email</p>
                <a href="mailto:bekasi.asiaone@gmail.com" class="modal-button btn-purple d-flex justify-content-center align-items-center">
                    <i class="fas fa-envelope"></i> Hubungi Kami
                </a>

                <p class="modal-contact-text">atau chat dengan kami via whatsapp</p>
                <a href="https://wa.me/628170888820" class="modal-button btn-green d-flex justify-content-center align-items-center">
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
