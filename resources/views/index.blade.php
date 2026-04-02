<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portoku — Portfolio Profesional</title>
    <meta name="description" content="Portfolio profesional yang dapat dikustomisasi untuk semua bidang profesi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="/" class="logo">Porto<span>ku.</span></a>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Layanan</a></li>
                <li><a href="#portfolio">Portofolio</a></li>
                <li id="nav-auth-item"><a href="/login" class="btn btn-primary nav-btn">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="home" class="hero">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="container hero-container">
            <div class="hero-layout reveal">
                <!-- Hero Content (Left) -->
                <div class="hero-content">
                    <span class="badge" id="hero-badge">Profesional Portfolio</span>
                    <h3 class="hero-intro" id="profile-name">Nama Profesi</h3>
                    <h1 id="hero-headline">Wujudkan <span class="text-gradient">Ide</span> &amp; Proyek Terbaik Anda.</h1>
                    <p id="hero-description">Selamat datang di portfolio saya. Temukan karya dan layanan yang saya tawarkan.</p>
                    <p id="profile-bio" class="hero-bio"></p>
                    <div class="hero-actions">
                        <a href="#portfolio" class="btn btn-primary" id="hero-btn1">Lihat Karya Saya</a>
                        <a href="#contact" class="btn btn-outline" id="hero-btn2">Konsultasi Gratis</a>
                    </div>
                    <div class="profile-social" id="profile-social"></div>
                </div>

                <!-- Hero Photo (Right) — Large portrait -->
                <div class="hero-photo">
                    <img id="profile-photo" src="" alt="Profile Photo" class="profile-photo-large">
                    <div class="hero-photo-placeholder" id="profile-placeholder">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="hero-photo-glow"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Services -->
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title reveal">Keahlian <span class="text-gradient">Utama</span></h2>
            <div class="services-grid" id="services-grid"></div>
        </div>
    </section>

    <!-- Portfolio -->
    <section id="portfolio" class="portfolio">
        <div class="container">
            <h2 class="section-title reveal">Portofolio &amp; <span class="text-gradient">Karya</span></h2>
            <p class="section-subtitle reveal">Kumpulan karya dan proyek terbaik yang pernah saya kerjakan.</p>
            <div class="portfolio-grid" id="portfolio-grid"></div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="video-modal-overlay" id="videoModal">
        <div class="video-modal">
            <button class="video-modal-close" onclick="closeVideoModal()"><i class="fa-solid fa-xmark"></i></button>
            <div class="video-modal-content" id="videoModalContent"></div>
        </div>
    </div>

    <!-- Contact -->
    <section id="contact" class="contact">
        <div class="container relative">
            <div class="contact-box glass reveal">
                <h2 id="contact-title">Mari Berkolaborasi!</h2>
                <p id="contact-desc">Hubungi saya untuk informasi lebih lanjut atau penawaran kerja sama.</p>
                <a id="contact-email-btn" href="#" class="btn btn-primary pulse-effect">Kirim Email ke Saya</a>
                <div class="social-links" id="social-links"></div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container footer-content">
            <p>&copy; 2024 Portoku. Semua hak dilindungi.</p>
            <a href="/admin" class="admin-link" title="Admin Panel"><i class="fa-solid fa-gear"></i></a>
        </div>
    </footer>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
