<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — Portoku.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="logo">Porto<span>ku.</span></a>
            <span class="admin-badge" id="role-badge">User</span>
        </div>
        <nav class="sidebar-nav">
            <a href="#" class="nav-item active" data-section="dashboard"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
            <a href="#" class="nav-item" data-section="profile-editor"><i class="fa-solid fa-user-circle"></i> Profil &amp; Foto</a>
            <a href="#" class="nav-item" data-section="hero-editor"><i class="fa-solid fa-home"></i> Hero / Headline</a>
            <a href="#" class="nav-item" data-section="portfolio-editor"><i class="fa-solid fa-briefcase"></i> Portofolio</a>
            <a href="#" class="nav-item" data-section="services-editor"><i class="fa-solid fa-cogs"></i> Layanan</a>
            <a href="#" class="nav-item" data-section="contact-editor"><i class="fa-solid fa-envelope"></i> Kontak &amp; Sosial</a>
            <a href="#" class="nav-item admin-only" data-section="users-manager" style="display:none;"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
        </nav>
        <div class="sidebar-footer">
            <a href="#" class="btn btn-outline-sm" id="btnMyPortfolio"><i class="fa-solid fa-eye"></i> Lihat Portfolio Saya</a>
            <button class="btn btn-danger" id="btnLogout" style="width:100%;margin-top:8px;"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
            <h2 id="page-title">Dashboard</h2>
            <div class="topbar-actions">
                <span class="topbar-user" id="topbar-user"></span>
            </div>
        </header>

        <!-- Dashboard -->
        <section class="panel active" id="dashboard">
            <div class="stats-grid">
                <div class="stat-card glass">
                    <i class="fa-solid fa-briefcase"></i>
                    <div><h3 id="stat-total-projects">0</h3><p>Total Proyek</p></div>
                </div>
                <div class="stat-card glass">
                    <i class="fa-solid fa-eye"></i>
                    <div><h3 id="stat-visible-projects">0</h3><p>Proyek Tampil</p></div>
                </div>
                <div class="stat-card glass">
                    <i class="fa-solid fa-eye-slash"></i>
                    <div><h3 id="stat-hidden-projects">0</h3><p>Proyek Tersembunyi</p></div>
                </div>
            </div>
            <div class="quick-actions glass">
                <h3><i class="fa-solid fa-bolt"></i> Aksi Cepat</h3>
                <div class="actions-row">
                    <button class="btn btn-accent" onclick="switchSection('portfolio-editor')"><i class="fa-solid fa-plus"></i> Tambah Proyek</button>
                    <button class="btn btn-outline-sm" id="btnPreviewSite"><i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Situs</button>
                </div>
            </div>
        </section>

        <!-- Profile & Photo Editor -->
        <section class="panel" id="profile-editor">
            <div class="editor-card glass">
                <h3><i class="fa-solid fa-user-circle"></i> Profil &amp; Foto</h3>
                <div class="profile-upload-area">
                    <div class="current-photo-wrapper">
                        <img id="current-photo" src="" alt="Foto Profil" style="display:none;">
                        <div class="photo-placeholder" id="photo-placeholder"><i class="fa-solid fa-user"></i></div>
                    </div>
                    <div class="upload-controls">
                        <input type="file" id="photo-file-input" accept="image/*" style="display:none;">
                        <button class="btn btn-accent" onclick="document.getElementById('photo-file-input').click()"><i class="fa-solid fa-camera"></i> Upload Foto</button>
                        <p class="form-hint">JPG, PNG, WebP. Maks 5MB.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="prof-fullname" placeholder="Nama Anda">
                </div>
                <div class="form-group">
                    <label>Bio Singkat</label>
                    <textarea id="prof-bio" rows="3" placeholder="Ceritakan tentang diri Anda secara singkat..."></textarea>
                </div>
                <button class="btn btn-primary-sm" onclick="saveProfileData()"><i class="fa-solid fa-check"></i> Simpan Profil</button>
            </div>
        </section>

        <!-- Hero Editor -->
        <section class="panel" id="hero-editor">
            <div class="editor-card glass">
                <h3><i class="fa-solid fa-pen"></i> Edit Hero &amp; Headline</h3>
                <div class="form-group">
                    <label>Badge / Subtitle</label>
                    <input type="text" id="hero-badge" placeholder="contoh: 🔥 Digital Marketing Specialist">
                </div>
                <div class="form-group">
                    <label>Headline Utama</label>
                    <input type="text" id="hero-headline" placeholder="contoh: Meningkatkan Brand Awareness & Konversi Anda.">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea id="hero-description" rows="4" placeholder="Deskripsi singkat tentang diri Anda..."></textarea>
                </div>
                <button class="btn btn-primary-sm" onclick="saveHeroData()"><i class="fa-solid fa-check"></i> Simpan Hero</button>
            </div>
        </section>

        <!-- Portfolio Editor -->
        <section class="panel" id="portfolio-editor">
            <div class="editor-card glass">
                <div class="editor-header">
                    <h3><i class="fa-solid fa-briefcase"></i> Kelola Portofolio</h3>
                    <button class="btn btn-accent" id="btnAddProject"><i class="fa-solid fa-plus"></i> Tambah Proyek</button>
                </div>
                <p class="editor-desc">Tambah, edit, sembunyikan, atau hapus item portofolio.</p>
                <div id="project-list"></div>
            </div>

            <!-- Project Modal -->
            <div class="modal-overlay" id="projectModal">
                <div class="modal glass">
                    <div class="modal-header">
                        <h3 id="modal-title">Tambah Proyek Baru</h3>
                        <button class="btn-icon" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul Proyek</label>
                            <input type="text" id="proj-title" placeholder="contoh: Optimasi Meta Ads Campaign">
                        </div>
                        <div class="form-group">
                            <label>Kategori / Tag</label>
                            <input type="text" id="proj-tag" placeholder="contoh: Performance Ads">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea id="proj-desc" rows="3" placeholder="Penjelasan singkat..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>URL Gambar / Thumbnail</label>
                            <input type="text" id="proj-image" placeholder="path gambar atau URL online">
                            <div class="image-upload-row">
                                <input type="file" id="proj-file-input" accept="image/*" style="display:none;">
                                <button class="btn btn-outline-sm" onclick="document.getElementById('proj-file-input').click()" style="margin-top:8px;"><i class="fa-solid fa-upload"></i> Upload Gambar</button>
                            </div>
                            <div class="image-preview" id="image-preview"></div>
                        </div>
                        <div class="form-group">
                            <label>Link Akun / Tautan Eksternal</label>
                            <input type="url" id="proj-link" placeholder="contoh: https://instagram.com/akun-client">
                        </div>
                        <div class="form-group">
                            <label>Link/Embed Video (YouTube / Lainnya)</label>
                            <input type="url" id="proj-video" placeholder="contoh: https://youtube.com/watch?v=...">
                        </div>
                        <div class="form-group toggle-group">
                            <label>Tampilkan ke Pengunjung</label>
                            <label class="toggle">
                                <input type="checkbox" id="proj-visible" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-sm" onclick="closeModal()">Batal</button>
                        <button class="btn btn-primary-sm" id="btnSaveProject"><i class="fa-solid fa-check"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Editor -->
        <section class="panel" id="services-editor">
            <div class="editor-card glass">
                <div class="editor-header">
                    <h3><i class="fa-solid fa-cogs"></i> Kelola Layanan</h3>
                    <button class="btn btn-accent" id="btnAddService"><i class="fa-solid fa-plus"></i> Tambah Layanan</button>
                </div>
                <div id="service-list"></div>
            </div>

            <div class="modal-overlay" id="serviceModal">
                <div class="modal glass">
                    <div class="modal-header">
                        <h3 id="service-modal-title">Tambah Layanan</h3>
                        <button class="btn-icon" onclick="closeServiceModal()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Layanan</label>
                            <input type="text" id="svc-title" placeholder="contoh: SEO &amp; Analytics">
                        </div>
                        <div class="form-group">
                            <label>Ikon (FontAwesome class)</label>
                            <input type="text" id="svc-icon" placeholder="contoh: fa-solid fa-chart-line">
                            <small>Lihat ikon di <a href="https://fontawesome.com/icons" target="_blank" style="color:var(--secondary-color);">fontawesome.com/icons</a></small>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea id="svc-desc" rows="3" placeholder="Penjelasan singkat layanan..."></textarea>
                        </div>
                        <div class="form-group toggle-group">
                            <label>Tampilkan</label>
                            <label class="toggle">
                                <input type="checkbox" id="svc-visible" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-sm" onclick="closeServiceModal()">Batal</button>
                        <button class="btn btn-primary-sm" id="btnSaveService"><i class="fa-solid fa-check"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Editor -->
        <section class="panel" id="contact-editor">
            <div class="editor-card glass">
                <h3><i class="fa-solid fa-envelope"></i> Edit Kontak &amp; Media Sosial</h3>
                <div class="form-group">
                    <label>Judul CTA</label>
                    <input type="text" id="contact-title" placeholder="contoh: Mari Berkolaborasi!">
                </div>
                <div class="form-group">
                    <label>Deskripsi CTA</label>
                    <textarea id="contact-desc" rows="3" placeholder="Ajakan singkat..."></textarea>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="contact-email" placeholder="email@domain.com">
                </div>
                <hr style="border-color: rgba(255,255,255,0.05); margin: 24px 0;">
                <h4 style="margin-bottom: 16px; color: var(--text-muted);">Link Media Sosial</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fa-brands fa-linkedin"></i> LinkedIn</label>
                        <input type="url" id="social-linkedin" placeholder="https://linkedin.com/in/...">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-brands fa-instagram"></i> Instagram</label>
                        <input type="url" id="social-instagram" placeholder="https://instagram.com/...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fa-brands fa-whatsapp"></i> WhatsApp</label>
                        <input type="tel" id="social-whatsapp" placeholder="628123456789 (tanpa + atau 0)">
                        <small style="color:var(--text-muted);font-size:0.75rem;">Format: 628xxx (kode negara tanpa +)</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fa-brands fa-tiktok"></i> TikTok</label>
                        <input type="url" id="social-tiktok" placeholder="https://tiktok.com/@...">
                    </div>
                </div>
                <button class="btn btn-primary-sm" onclick="saveContactData()"><i class="fa-solid fa-check"></i> Simpan Kontak</button>
            </div>
        </section>

        <!-- User Management (Admin Only) -->
        <section class="panel" id="users-manager">
            <div class="editor-card glass">
                <div class="editor-header">
                    <h3><i class="fa-solid fa-users-gear"></i> Kelola Semua User</h3>
                    <button class="btn btn-accent" id="btnAddUser"><i class="fa-solid fa-user-plus"></i> Tambah User</button>
                </div>
                <p class="editor-desc">Lihat, kelola role, atau hapus user yang terdaftar.</p>
                <div id="users-list"></div>
            </div>

            <!-- Modal Tambah User -->
            <div class="modal-overlay" id="addUserModal">
                <div class="modal glass">
                    <div class="modal-header">
                        <h3><i class="fa-solid fa-user-plus"></i> Tambah User Baru</h3>
                        <button class="btn-icon" onclick="closeAddUserModal()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" id="new-user-fullname" placeholder="Nama lengkap user">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="new-user-username" placeholder="username (tanpa spasi)">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="new-user-email" placeholder="email@domain.com">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="new-user-password" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select id="new-user-role" style="width:100%;padding:12px 16px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;color:var(--text-primary);font-size:0.95rem;">
                                <option value="user">User (Portfolio biasa)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-sm" onclick="closeAddUserModal()">Batal</button>
                        <button class="btn btn-primary-sm" id="btnSaveNewUser"><i class="fa-solid fa-check"></i> Buat Akun</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Generic Confirm Modal -->
        <div class="modal-overlay" id="confirmModal">
            <div class="modal glass" style="max-width: 400px; text-align: center; padding: 32px 24px;">
                <div style="font-size: 3rem; color: var(--accent-red); margin-bottom: 16px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 id="confirm-title" style="margin-bottom: 8px;">Konfirmasi</h3>
                <p id="confirm-message" style="color: var(--text-muted); margin-bottom: 24px;">Apakah Anda yakin?</p>
                <div class="modal-footer" style="justify-content: center; border: none; padding: 0;">
                    <button class="btn btn-outline-sm" onclick="closeConfirmModal()">Batal</button>
                    <button class="btn btn-danger" id="btnConfirmAction" style="padding: 10px 20px;">Yakin</button>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
