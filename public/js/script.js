// =========================================
//  Frontend Script — Laravel 11 version
//  API Base: /api/ (Laravel routes)
// =========================================

const API_BASE = '/api/';

// Get username from URL: ?user=xxx
function getTargetUser() {
    const params = new URLSearchParams(window.location.search);
    return params.get('user') || '';
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// ---- Init ----
document.addEventListener('DOMContentLoaded', () => {
    const user = getTargetUser();
    loadProfile(user);
    loadServices(user);
    loadPortfolio(user);
    loadContact(user);
    checkLoginStatus();
    initMobileMenu();
    reveal();
});

// ---- Mobile Hamburger Menu ----
function initMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    if (!hamburger || !navLinks) return;

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('open');
    });

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navLinks.classList.remove('open');
        });
    });
}

// ---- Navbar Scroll ----
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    reveal();
});

// ---- Scroll Reveal ----
function reveal() {
    const reveals = document.querySelectorAll('.reveal');
    for (let i = 0; i < reveals.length; i++) {
        const windowHeight = window.innerHeight;
        const elementTop = reveals[i].getBoundingClientRect().top;
        if (elementTop < windowHeight - 100) {
            reveals[i].classList.add('active');
        }
    }
}

// ---- Check if logged in ----
async function checkLoginStatus() {
    try {
        const res = await fetch(API_BASE + 'auth/check');
        const data = await res.json();
        const navItem = document.getElementById('nav-auth-item');
        if (data.logged_in) {
            navItem.innerHTML = `
                <a href="/admin" class="btn btn-primary nav-btn">
                    <i class="fa-solid fa-grid-2"></i> Dashboard
                </a>`;
        }
    } catch (e) {
        // API not available — keep default link
    }
}

// ---- Load Profile ----
async function loadProfile(username) {
    try {
        const url = username
            ? API_BASE + 'profile?user=' + encodeURIComponent(username)
            : API_BASE + 'profile';
        const res = await fetch(url);
        const data = await res.json();

        if (data.success && data.profile) {
            const p = data.profile;

            const photo = document.getElementById('profile-photo');
            const placeholder = document.getElementById('profile-placeholder');
            if (p.profile_photo) {
                photo.src = p.profile_photo;
                photo.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                photo.style.display = 'none';
                placeholder.style.display = 'flex';
            }

            document.getElementById('profile-name').textContent = p.full_name || p.username || 'Digital Marketer';
            document.getElementById('profile-bio').textContent = p.bio || 'Spesialis pemasaran digital.';

            document.getElementById('hero-badge').textContent = p.badge || 'Profesional Portfolio';
            document.getElementById('hero-headline').innerHTML = p.headline || 'Wujudkan <span class="text-gradient">Ide</span> &amp; Proyek Terbaik Anda.';
            document.getElementById('hero-description').textContent = p.description || '';

            document.title = (p.full_name || p.username) + ' — Portfolio Profesional';
        }
    } catch (e) {
        // API not available, keep defaults
    }
}

// ---- Load Services ----
async function loadServices(username) {
    try {
        const url = username
            ? API_BASE + 'services?user=' + encodeURIComponent(username)
            : API_BASE + 'services';
        const res = await fetch(url);
        const data = await res.json();

        if (data.success && data.services) {
            renderServices(data.services);
            return;
        }
    } catch (e) {
        // Fallback — empty
    }

    // Fallback default — generic
    renderServices([
        { title: 'Layanan Utama', icon: 'fa-solid fa-star', description: 'Layanan unggulan yang saya tawarkan untuk membantu klien mencapai tujuan mereka.' },
        { title: 'Konsultasi Profesional', icon: 'fa-solid fa-comments', description: 'Diskusikan kebutuhan Anda dan dapatkan solusi terbaik yang sesuai.' },
        { title: 'Proyek Kolaborasi', icon: 'fa-solid fa-handshake', description: 'Bekerja sama untuk mewujudkan ide dan proyek Anda menjadi kenyataan.' }
    ]);
}

function renderServices(services) {
    const container = document.getElementById('services-grid');
    if (!services.length) {
        container.innerHTML = '<p style="text-align:center;color:#a0a0b0;grid-column:1/-1;">Belum ada layanan.</p>';
        return;
    }
    container.innerHTML = services.map((s, i) => `
        <div class="service-card glass reveal delay-${(i % 3) + 1}">
            <i class="${escapeHtml(s.icon)}"></i>
            <h3>${escapeHtml(s.title)}</h3>
            <p>${escapeHtml(s.description)}</p>
        </div>
    `).join('');
    setTimeout(reveal, 50);
}

// ---- Load Portfolio ----
async function loadPortfolio(username) {
    try {
        const url = username
            ? API_BASE + 'portfolio?user=' + encodeURIComponent(username)
            : API_BASE + 'portfolio';
        const res = await fetch(url);
        const data = await res.json();

        if (data.success && data.projects) {
            renderPortfolio(data.projects);
            return;
        }
    } catch (e) {
        // Fallback
    }

    // Default fallback — generic
    renderPortfolio([
        { title: 'Contoh Proyek 1', tag: 'Karya', description: 'Deskripsi proyek ini akan tampil di sini.', image: '', link: '#', video: '' },
        { title: 'Contoh Proyek 2', tag: 'Karya', description: 'Deskripsi proyek ini akan tampil di sini.', image: '', link: '#', video: '' },
    ]);
}

function renderPortfolio(projects) {
    const container = document.getElementById('portfolio-grid');
    if (!projects.length) {
        container.innerHTML = '<p style="text-align:center;color:#a0a0b0;grid-column:1/-1;">Belum ada proyek.</p>';
        return;
    }

    container.innerHTML = projects.map((p, i) => {
        const hasVideo = p.video && p.video.trim() !== '';
        const hasLink = p.link && p.link.trim() !== '' && p.link !== '#';
        const overlayIcon = hasVideo ? 'fa-play' : 'fa-link';

        return `
        <div class="portfolio-card glass reveal delay-${(i % 3) + 1}">
            <div class="portfolio-img">
                ${p.image ? `<img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.title)}" onerror="this.style.display='none'">` : '<div class="placeholder-img"><i class="fa-solid fa-image"></i></div>'}
                <div class="portfolio-overlay">
                    ${hasVideo
                        ? `<button class="btn btn-circle" onclick="openVideoModal('${escapeHtml(p.video)}')" style="border:none;cursor:pointer;"><i class="fa-solid ${overlayIcon}"></i></button>`
                        : (hasLink
                            ? `<a href="${escapeHtml(p.link)}" target="_blank" class="btn btn-circle"><i class="fa-solid ${overlayIcon}"></i></a>`
                            : `<span class="btn btn-circle"><i class="fa-solid fa-eye"></i></span>`
                        )
                    }
                </div>
            </div>
            <div class="portfolio-info">
                <span class="tag">${escapeHtml(p.tag)}</span>
                <h3>${escapeHtml(p.title)}</h3>
                <p>${escapeHtml(p.description)}</p>
                <div class="portfolio-links">
                    ${hasLink ? `<a href="${escapeHtml(p.link)}" target="_blank" class="project-link">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>` : ''}
                    ${hasVideo ? `<button class="project-link video-link" onclick="openVideoModal('${escapeHtml(p.video)}')"><i class="fa-solid fa-play"></i> Video</button>` : ''}
                </div>
            </div>
        </div>`;
    }).join('');
    setTimeout(reveal, 50);
}

// ---- Load Contact ----
async function loadContact(username) {
    try {
        const url = username
            ? API_BASE + 'contact?user=' + encodeURIComponent(username)
            : API_BASE + 'contact';
        const res = await fetch(url);
        const data = await res.json();

        if (data.success && data.contact) {
            const c = data.contact;
            document.getElementById('contact-title').textContent = c.cta_title || 'Mari Berkolaborasi!';
            document.getElementById('contact-desc').textContent = c.cta_description || '';

            const emailBtn = document.getElementById('contact-email-btn');
            if (c.email) emailBtn.href = 'mailto:' + c.email;

            renderSocialLinks(c);
            return;
        }
    } catch (e) {
        // Fallback
    }

    document.getElementById('social-links').innerHTML = `
        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-whatsapp"></i></a>`;
}

function renderSocialLinks(contact) {
    const container = document.getElementById('social-links');

    // Email: render sebagai link mailto:
    const emailHtml = (contact.email && contact.email.trim())
        ? `<a href="mailto:${escapeHtml(contact.email)}" title="Email"><i class="fa-solid fa-envelope"></i></a>`
        : '';

    const socials = [
        // WhatsApp: nomor → wa.me link
        {
            key: 'whatsapp',
            icon: 'fa-brands fa-whatsapp',
            href: (val) => `https://wa.me/${val.replace(/\D/g, '')}`
        },
        { key: 'linkedin',  icon: 'fa-brands fa-linkedin',  href: (val) => val },
        { key: 'instagram', icon: 'fa-brands fa-instagram', href: (val) => val },
        { key: 'tiktok',    icon: 'fa-brands fa-tiktok',    href: (val) => val },
    ];

    const socialsHtml = socials
        .filter(s => contact[s.key] && contact[s.key].trim() !== '')
        .map(s => `<a href="${escapeHtml(s.href(contact[s.key]))}" target="_blank" rel="noopener" title="${s.key}"><i class="${s.icon}"></i></a>`)
        .join('');

    const html = emailHtml + socialsHtml;

    container.innerHTML = html || `
        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-whatsapp"></i></a>`;

    const profileSocial = document.getElementById('profile-social');
    if (profileSocial) {
        profileSocial.innerHTML = html;
    }
}

// ---- Video Modal ----
function openVideoModal(url) {
    const modal = document.getElementById('videoModal');
    const content = document.getElementById('videoModalContent');

    const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/);
    if (ytMatch) {
        content.innerHTML = `<iframe src="https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    } else {
        content.innerHTML = `<p style="text-align:center;color:white;">
            <a href="${escapeHtml(url)}" target="_blank" class="btn btn-primary" style="margin-top:20px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Video di Tab Baru
            </a></p>`;
    }
    modal.classList.add('open');
}

function closeVideoModal() {
    document.getElementById('videoModal').classList.remove('open');
    document.getElementById('videoModalContent').innerHTML = '';
}
