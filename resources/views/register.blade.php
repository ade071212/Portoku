<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Portoku.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>

        <div class="auth-card glass">
            <div class="auth-header">
                <a href="/" class="logo">Porto<span>ku.</span></a>
                <p>Buat akun portfolio Anda</p>
            </div>

            <form id="registerForm" autocomplete="on">
                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-fullname"><i class="fa-solid fa-id-card"></i> Nama Lengkap</label>
                        <input type="text" id="reg-fullname" name="full_name" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-username"><i class="fa-solid fa-at"></i> Username</label>
                        <input type="text" id="reg-username" name="username" placeholder="username_unik" required pattern="[a-zA-Z0-9_]+" autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="reg-email" name="email" placeholder="email@domain.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="reg-password"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="reg-password" name="password" placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePassword('reg-password', this)"><i class="fa-solid fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-password2"><i class="fa-solid fa-shield-halved"></i> Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="reg-password2" placeholder="Ulangi password" required minlength="6" autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePassword('reg-password2', this)"><i class="fa-solid fa-eye"></i></button>
                    </div>
                </div>

                <div id="error-msg" class="error-msg"></div>

                <button type="submit" class="btn btn-primary" id="btnRegister">
                    <span class="btn-text">Daftar Sekarang</span>
                    <span class="btn-loader" style="display:none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
            </form>

            <div class="auth-footer">
                <p>Sudah punya akun? <a href="/login">Masuk di sini</a></p>
                <a href="/" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Home</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnRegister');
            const errorMsg = document.getElementById('error-msg');
            errorMsg.textContent = '';
            errorMsg.classList.remove('show');

            const password = document.getElementById('reg-password').value;
            const password2 = document.getElementById('reg-password2').value;

            if (password !== password2) {
                errorMsg.textContent = 'Password dan konfirmasi password tidak cocok.';
                errorMsg.classList.add('show');
                return;
            }

            setBtnLoading(btn, true);
            try {
                const res = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        username: document.getElementById('reg-username').value,
                        email: document.getElementById('reg-email').value,
                        password: password,
                        full_name: document.getElementById('reg-fullname').value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showSuccess('Registrasi berhasil! Mengalihkan ke dashboard...');
                    setTimeout(() => window.location.href = '/admin', 1000);
                } else {
                    errorMsg.textContent = data.error || 'Registrasi gagal.';
                    errorMsg.classList.add('show');
                }
            } catch (err) {
                errorMsg.textContent = 'Koneksi ke server gagal.';
                errorMsg.classList.add('show');
            }
            setBtnLoading(btn, false);
        });
    </script>
</body>
</html>
