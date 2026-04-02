// ============================================
//  Admin Panel — Full API-based Logic
//  Laravel 11 version — updated API URLs
// ============================================

// Get CSRF token from meta tag
function getCsrf() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
}

let currentUser = null;
let projects = [];
let services = [];
let editingProjectId = null;
let editingServiceId = null;

// ---- Custom Confirm Modal ----
let confirmCallback = null;

function customConfirm(title, message, callback) {
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-message').textContent = message;
    document.getElementById('confirmModal').classList.add('open');
    confirmCallback = callback;
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('open');
    confirmCallback = null;
}

document.addEventListener('DOMContentLoaded', async () => {
    // init confirm button
    document.getElementById('btnConfirmAction')?.addEventListener('click', () => {
        if (typeof confirmCallback === 'function') confirmCallback();
        closeConfirmModal();
    });
    await checkAuth();
    initNavigation();
    initModalHandlers();
    initUploadHandlers();
    loadAllData();
});

// ---- Auth Check ----
async function checkAuth() {
    try {
        const res = await fetch('/api/auth/check');
        const data = await res.json();
        if (!data.logged_in) {
            window.location.href = '/login';
            return;
        }
        currentUser = data.user;

        document.getElementById('topbar-user').innerHTML =
            `<i class="fa-solid fa-user-circle"></i> ${currentUser.full_name || currentUser.username}`;

        const badge = document.getElementById('role-badge');
        badge.textContent = currentUser.role === 'admin' ? 'Admin' : 'User';

        if (currentUser.role === 'admin') {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'flex');
        }

        document.getElementById('btnMyPortfolio').href = '/?user=' + currentUser.username;
        document.getElementById('btnMyPortfolio').target = '_blank';
        document.getElementById('btnPreviewSite').onclick = () =>
            window.open('/?user=' + currentUser.username, '_blank');

    } catch (e) {
        showToast('Koneksi ke server gagal.', true);
    }
}

// ---- Load All Data ----
async function loadAllData() {
    await Promise.all([
        loadProfileData(),
        loadProjects(),
        loadServices(),
        loadContactData(),
        loadUsers()
    ]);
    updateStats();
}

// ---- Navigation ----
function initNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            switchSection(item.dataset.section);
        });
    });

    document.getElementById('menuToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });

    document.getElementById('btnLogout').addEventListener('click', () => {
        customConfirm('Logout', 'Yakin ingin keluar dari panel admin?', async () => {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrf() }
            });
            window.location.href = '/login';
        });
    });
}

function switchSection(sectionId) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById(sectionId)?.classList.add('active');

    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.querySelector(`.nav-item[data-section="${sectionId}"]`)?.classList.add('active');

    const titles = {
        'dashboard': 'Dashboard',
        'profile-editor': 'Profil & Foto',
        'hero-editor': 'Hero & Headline',
        'portfolio-editor': 'Kelola Portofolio',
        'services-editor': 'Kelola Layanan',
        'contact-editor': 'Kontak & Sosial',
        'users-manager': 'Kelola User'
    };
    document.getElementById('page-title').textContent = titles[sectionId] || 'Dashboard';
    document.getElementById('sidebar').classList.remove('open');
}

// ---- Dashboard Stats ----
function updateStats() {
    const total = projects.length;
    const visible = projects.filter(p => p.visible == 1 || p.visible === true).length;
    document.getElementById('stat-total-projects').textContent = total;
    document.getElementById('stat-visible-projects').textContent = visible;
    document.getElementById('stat-hidden-projects').textContent = total - visible;
}

// ---- Profile Editor ----
async function loadProfileData() {
    try {
        const res = await fetch('/api/profile?user=' + (currentUser?.username || ''));
        const data = await res.json();
        if (data.success && data.profile) {
            const p = data.profile;
            document.getElementById('prof-fullname').value = p.full_name || '';
            document.getElementById('prof-bio').value = p.bio || '';
            document.getElementById('hero-badge').value = p.badge || '';
            document.getElementById('hero-headline').value = p.headline || '';
            document.getElementById('hero-description').value = p.description || '';

            const photo = document.getElementById('current-photo');
            const placeholder = document.getElementById('photo-placeholder');
            if (p.profile_photo) {
                photo.src = p.profile_photo;
                photo.style.display = 'block';
                placeholder.style.display = 'none';
            }
        }
    } catch (e) { /* ignore */ }
}

async function saveProfileData() {
    try {
        const res = await fetch('/api/profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({
                full_name: document.getElementById('prof-fullname').value,
                bio: document.getElementById('prof-bio').value
            })
        });
        const data = await res.json();
        showToast(data.success ? 'Profil disimpan!' : (data.error || 'Gagal'));
    } catch (e) {
        showToast('Gagal menyimpan profil.', true);
    }
}

async function saveHeroData() {
    try {
        const res = await fetch('/api/profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({
                badge: document.getElementById('hero-badge').value,
                headline: document.getElementById('hero-headline').value,
                description: document.getElementById('hero-description').value
            })
        });
        const data = await res.json();
        showToast(data.success ? 'Hero disimpan!' : (data.error || 'Gagal'));
    } catch (e) {
        showToast('Gagal menyimpan hero.', true);
    }
}

// ---- Upload Handlers ----
function initUploadHandlers() {
    // Profile photo upload
    document.getElementById('photo-file-input').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch('/api/upload?type=profile', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrf() },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('current-photo').src = data.path;
                document.getElementById('current-photo').style.display = 'block';
                document.getElementById('photo-placeholder').style.display = 'none';
                showToast('Foto profil diperbarui!');
            } else {
                showToast(data.error || 'Gagal upload.', true);
            }
        } catch (e) {
            showToast('Gagal upload foto.', true);
        }
    });

    // Project image upload
    document.getElementById('proj-file-input').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch('/api/upload?type=image', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrf() },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('proj-image').value = data.path;
                document.getElementById('image-preview').innerHTML =
                    `<img src="${data.path}" alt="Preview">`;
                showToast('Gambar diupload!');
            } else {
                showToast(data.error || 'Gagal upload.', true);
            }
        } catch (e) {
            showToast('Gagal upload gambar.', true);
        }
    });
}

// ---- Portfolio / Projects ----
async function loadProjects() {
    try {
        const res = await fetch('/api/portfolio/all');
        const data = await res.json();
        if (data.success) {
            projects = data.projects;
            renderProjectList();
        }
    } catch (e) { /* ignore */ }
}

function renderProjectList() {
    const container = document.getElementById('project-list');
    if (!projects.length) {
        container.innerHTML = `<div class="empty-state"><i class="fa-solid fa-folder-open"></i><p>Belum ada proyek.</p></div>`;
        return;
    }

    container.innerHTML = projects.map(p => {
        const isVisible = p.visible == 1 || p.visible === true;
        return `
        <div class="project-item ${!isVisible ? 'hidden-project' : ''}" data-id="${p.id}">
            <div class="project-thumb">
                ${p.image ? `<img src="${p.image}" alt="${p.title}" onerror="this.style.display='none'">` : ''}
            </div>
            <div class="project-meta">
                <h4>${p.title}</h4>
                <span class="tag-sm">${p.tag}</span>
                <span class="visibility-label ${isVisible ? 'visible' : 'hidden'}">
                    <i class="fa-solid ${isVisible ? 'fa-eye' : 'fa-eye-slash'}"></i>
                    ${isVisible ? 'Tampil' : 'Tersembunyi'}
                </span>
            </div>
            <div class="project-actions">
                <button class="btn-icon" title="Toggle" onclick="toggleProject(${p.id})">
                    <i class="fa-solid ${isVisible ? 'fa-eye-slash' : 'fa-eye'}"></i>
                </button>
                <button class="btn-icon" title="Edit" onclick="editProject(${p.id})">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn-icon" title="Hapus" onclick="deleteProject(${p.id})" style="color:var(--accent-red);">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>`;
    }).join('');
}

async function toggleProject(id) {
    await fetch(`/api/portfolio/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': getCsrf() }
    });
    await loadProjects();
    updateStats();
    showToast('Visibilitas diperbarui.');
}

async function deleteProject(id) {
    customConfirm('Hapus Proyek', 'Yakin hapus proyek ini? Data tidak bisa dikembalikan.', async () => {
        await fetch(`/api/portfolio/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrf() }
        });
        await loadProjects();
        updateStats();
        showToast('Proyek dihapus.');
    });
}

// ---- Project Modal ----
function initModalHandlers() {
    document.getElementById('btnAddProject').addEventListener('click', () => {
        editingProjectId = null;
        document.getElementById('modal-title').textContent = 'Tambah Proyek Baru';
        clearProjectForm();
        openModal();
    });

    document.getElementById('btnSaveProject').addEventListener('click', saveProject);

    document.getElementById('proj-image').addEventListener('input', (e) => {
        const url = e.target.value;
        const preview = document.getElementById('image-preview');
        preview.innerHTML = url ? `<img src="${url}" alt="Preview" onerror="this.parentElement.innerHTML='<p style=\\'color:var(--accent-red);font-size:0.8rem;\\'>Gambar tidak dapat dimuat</p>'">` : '';
    });

    document.getElementById('btnAddService').addEventListener('click', () => {
        editingServiceId = null;
        document.getElementById('service-modal-title').textContent = 'Tambah Layanan';
        clearServiceForm();
        openServiceModal();
    });

    document.getElementById('btnSaveService').addEventListener('click', saveService);

    // Tambah User modal (admin only)
    document.getElementById('btnAddUser')?.addEventListener('click', openAddUserModal);
    document.getElementById('btnSaveNewUser')?.addEventListener('click', saveNewUser);
}

function openModal() { document.getElementById('projectModal').classList.add('open'); }
function closeModal() { document.getElementById('projectModal').classList.remove('open'); editingProjectId = null; }

function clearProjectForm() {
    ['proj-title','proj-tag','proj-desc','proj-image','proj-link','proj-video'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('proj-visible').checked = true;
    document.getElementById('image-preview').innerHTML = '';
}

function editProject(id) {
    const p = projects.find(x => x.id == id);
    if (!p) return;
    editingProjectId = id;
    document.getElementById('modal-title').textContent = 'Edit Proyek';
    document.getElementById('proj-title').value = p.title || '';
    document.getElementById('proj-tag').value = p.tag || '';
    document.getElementById('proj-desc').value = p.description || '';
    document.getElementById('proj-image').value = p.image || '';
    document.getElementById('proj-link').value = p.link || '';
    document.getElementById('proj-video').value = p.video || '';
    document.getElementById('proj-visible').checked = (p.visible == 1 || p.visible === true);
    const preview = document.getElementById('image-preview');
    preview.innerHTML = p.image ? `<img src="${p.image}" alt="Preview" onerror="this.style.display='none'">` : '';
    openModal();
}

async function saveProject() {
    const title = document.getElementById('proj-title').value.trim();
    if (!title) { alert('Judul wajib diisi!'); return; }

    const payload = {
        title,
        tag: document.getElementById('proj-tag').value.trim(),
        description: document.getElementById('proj-desc').value.trim(),
        image: document.getElementById('proj-image').value.trim(),
        link: document.getElementById('proj-link').value.trim(),
        video: document.getElementById('proj-video').value.trim(),
        visible: document.getElementById('proj-visible').checked ? 1 : 0
    };

    if (editingProjectId) {
        await fetch(`/api/portfolio/${editingProjectId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify(payload)
        });
    } else {
        await fetch('/api/portfolio', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify(payload)
        });
    }

    await loadProjects();
    updateStats();
    const wasEditing = editingProjectId;
    closeModal();
    showToast(wasEditing ? 'Proyek diperbarui!' : 'Proyek ditambahkan!');
}

// ---- Services ----
async function loadServices() {
    try {
        const res = await fetch('/api/services/all');
        const data = await res.json();
        if (data.success) {
            services = data.services;
            renderServiceList();
        }
    } catch (e) { /* ignore */ }
}

function renderServiceList() {
    const container = document.getElementById('service-list');
    if (!services.length) {
        container.innerHTML = `<div class="empty-state"><i class="fa-solid fa-cogs"></i><p>Belum ada layanan.</p></div>`;
        return;
    }

    container.innerHTML = services.map(s => {
        const isVisible = s.visible == 1 || s.visible === true;
        return `
        <div class="service-item ${!isVisible ? 'hidden-service' : ''}" data-id="${s.id}">
            <div class="service-icon-preview"><i class="${s.icon}"></i></div>
            <div class="project-meta">
                <h4>${s.title}</h4>
                <span class="visibility-label ${isVisible ? 'visible' : 'hidden'}">
                    <i class="fa-solid ${isVisible ? 'fa-eye' : 'fa-eye-slash'}"></i>
                    ${isVisible ? 'Tampil' : 'Tersembunyi'}
                </span>
            </div>
            <div class="project-actions">
                <button class="btn-icon" onclick="toggleService(${s.id})"><i class="fa-solid ${isVisible ? 'fa-eye-slash' : 'fa-eye'}"></i></button>
                <button class="btn-icon" onclick="editService(${s.id})"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-icon" onclick="deleteService(${s.id})" style="color:var(--accent-red);"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>`;
    }).join('');
}

async function toggleService(id) {
    await fetch(`/api/services/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': getCsrf() }
    });
    await loadServices();
    showToast('Visibilitas layanan diperbarui.');
}

async function deleteService(id) {
    customConfirm('Hapus Layanan', 'Yakin hapus layanan ini?', async () => {
        await fetch(`/api/services/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrf() }
        });
        await loadServices();
        showToast('Layanan dihapus.');
    });
}

function openServiceModal() { document.getElementById('serviceModal').classList.add('open'); }
function closeServiceModal() { document.getElementById('serviceModal').classList.remove('open'); editingServiceId = null; }

function clearServiceForm() {
    ['svc-title','svc-icon','svc-desc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('svc-visible').checked = true;
}

function editService(id) {
    const s = services.find(x => x.id == id);
    if (!s) return;
    editingServiceId = id;
    document.getElementById('service-modal-title').textContent = 'Edit Layanan';
    document.getElementById('svc-title').value = s.title || '';
    document.getElementById('svc-icon').value = s.icon || '';
    document.getElementById('svc-desc').value = s.description || '';
    document.getElementById('svc-visible').checked = (s.visible == 1 || s.visible === true);
    openServiceModal();
}

async function saveService() {
    const title = document.getElementById('svc-title').value.trim();
    if (!title) { alert('Nama layanan wajib diisi!'); return; }

    const payload = {
        title,
        icon: document.getElementById('svc-icon').value.trim() || 'fa-solid fa-star',
        description: document.getElementById('svc-desc').value.trim(),
        visible: document.getElementById('svc-visible').checked ? 1 : 0
    };

    if (editingServiceId) {
        await fetch(`/api/services/${editingServiceId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify(payload)
        });
    } else {
        await fetch('/api/services', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify(payload)
        });
    }

    await loadServices();
    const wasEditing = editingServiceId;
    closeServiceModal();
    showToast(wasEditing ? 'Layanan diperbarui!' : 'Layanan ditambahkan!');
}

// ---- Contact ----
async function loadContactData() {
    try {
        const res = await fetch('/api/contact');
        const data = await res.json();
        if (data.success && data.contact) {
            const c = data.contact;
            document.getElementById('contact-title').value = c.cta_title || '';
            document.getElementById('contact-desc').value = c.cta_description || '';
            document.getElementById('contact-email').value = c.email || '';
            document.getElementById('social-linkedin').value = c.linkedin || '';
            document.getElementById('social-instagram').value = c.instagram || '';
            document.getElementById('social-whatsapp').value = c.whatsapp || '';
            document.getElementById('social-tiktok').value = c.tiktok || '';
        }
    } catch (e) { /* ignore */ }
}

async function saveContactData() {
    const payload = {
        cta_title: document.getElementById('contact-title').value,
        cta_description: document.getElementById('contact-desc').value,
        email: document.getElementById('contact-email').value,
        linkedin: document.getElementById('social-linkedin').value,
        instagram: document.getElementById('social-instagram').value,
        whatsapp: document.getElementById('social-whatsapp').value,
        tiktok: document.getElementById('social-tiktok').value
    };
    try {
        const res = await fetch('/api/contact', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        showToast(data?.success ? 'Kontak disimpan!' : (data?.error || 'Gagal'));
    } catch (e) {
        showToast('Gagal menyimpan kontak.', true);
    }
}

// ---- User Management (Admin Only) ----
async function loadUsers() {
    if (!currentUser || currentUser.role !== 'admin') return;
    try {
        const res = await fetch('/api/users');
        const data = await res.json();
        if (data.success) renderUserList(data.users);
    } catch (e) { /* ignore */ }
}

function renderUserList(users) {
    const container = document.getElementById('users-list');
    if (!users.length) {
        container.innerHTML = '<div class="empty-state"><p>Tidak ada user.</p></div>';
        return;
    }

    container.innerHTML = users.map(u => `
        <div class="project-item" data-id="${u.id}">
            <div class="project-thumb" style="border-radius:50%;overflow:hidden;">
                ${u.profile_photo
                    ? `<img src="${u.profile_photo}" alt="${u.username}" style="border-radius:50%;">`
                    : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);color:var(--text-muted);font-size:1.5rem;"><i class="fa-solid fa-user"></i></div>`
                }
            </div>
            <div class="project-meta">
                <h4>${u.full_name || u.username} <span class="tag-sm">${u.role}</span></h4>
                <span style="color:var(--text-muted);font-size:0.8rem;">@${u.username} · ${u.email}</span>
            </div>
            <div class="project-actions">
                <button class="btn-icon" title="Lihat Portfolio" onclick="window.open('/?user=${u.username}','_blank')">
                    <i class="fa-solid fa-eye"></i>
                </button>
                ${u.role === 'user'
                    ? `<button class="btn-icon" title="Jadikan Admin" onclick="setUserRole(${u.id},'make-admin')"><i class="fa-solid fa-user-shield"></i></button>`
                    : `<button class="btn-icon" title="Cabut Admin" onclick="setUserRole(${u.id},'remove-admin')"><i class="fa-solid fa-user-minus"></i></button>`
                }
                ${u.id != currentUser.id
                    ? `<button class="btn-icon" title="Hapus User" onclick="deleteUser(${u.id})" style="color:var(--accent-red);"><i class="fa-solid fa-trash"></i></button>`
                    : ''
                }
            </div>
        </div>
    `).join('');
}

async function setUserRole(id, action) {
    await fetch(`/api/users/${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify({ id })
    });
    await loadUsers();
    showToast('Role user diperbarui.');
}

async function deleteUser(id) {
    customConfirm('Hapus User', 'Yakin hapus user ini? Semua data portfolio-nya akan terhapus.', async () => {
        await fetch('/api/users/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ id })
        });
        await loadUsers();
        showToast('User dihapus.');
    });
}

// ---- Add User Modal (Admin Only) ----
function openAddUserModal() {
    ['new-user-fullname','new-user-username','new-user-email','new-user-password'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('new-user-role').value = 'user';
    document.getElementById('addUserModal').classList.add('open');
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.remove('open');
}

async function saveNewUser() {
    const fullname  = document.getElementById('new-user-fullname').value.trim();
    const username  = document.getElementById('new-user-username').value.trim();
    const email     = document.getElementById('new-user-email').value.trim();
    const password  = document.getElementById('new-user-password').value;
    const role      = document.getElementById('new-user-role').value;

    if (!username || !email || !password) {
        showToast('Username, email, dan password wajib diisi!', true);
        return;
    }
    if (password.length < 6) {
        showToast('Password minimal 6 karakter!', true);
        return;
    }

    try {
        const res = await fetch('/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ full_name: fullname, username, email, password, role })
        });
        const data = await res.json();
        if (data.success) {
            closeAddUserModal();
            await loadUsers();
            showToast(`Akun "${username}" berhasil dibuat!`);
        } else {
            showToast(data.error || 'Gagal membuat akun.', true);
        }
    } catch (e) {
        showToast('Gagal terhubung ke server.', true);
    }
}

// ---- Toast ----
function showToast(message, isError = false) {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast';
    if (isError) toast.style.background = 'linear-gradient(135deg, #ff5252, #d32f2f)';
    toast.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 2500);
}
