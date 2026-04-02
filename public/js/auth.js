// ============================================
//  Auth shared utilities (Login & Register)
//  Laravel 11 version — updated API URLs
// ============================================

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function setBtnLoading(btn, loading) {
    const text = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.btn-loader');
    if (loading) {
        text.style.display = 'none';
        loader.style.display = 'inline-flex';
        btn.disabled = true;
    } else {
        text.style.display = 'inline';
        loader.style.display = 'none';
        btn.disabled = false;
    }
}

function showSuccess(message) {
    let toast = document.querySelector('.success-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'success-toast';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-check-circle"></i> ${message}`;
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

// Check if already logged in — redirect to admin
(async function checkExistingSession() {
    try {
        const res = await fetch('/api/auth/check');
        const data = await res.json();
        if (data.logged_in) {
            window.location.href = '/admin';
        }
    } catch (e) {
        // Server not running — that's fine
    }
})();
