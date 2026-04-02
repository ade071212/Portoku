<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Portoku.</title>
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
                <p>Masuk ke akun Anda</p>
            </div>

            <form id="loginForm" autocomplete="on">
                <div class="form-group">
                    <label for="login-input"><i class="fa-solid fa-user"></i> Username atau Email</label>
                    <input type="text" id="login-input" name="login" placeholder="Masukkan username atau email" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password-input"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password-input" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="togglePassword('password-input', this)"><i class="fa-solid fa-eye"></i></button>
                    </div>
                </div>

                <div id="error-msg" class="error-msg"></div>

                <button type="submit" class="btn btn-primary" id="btnLogin">
                    <span class="btn-text">Masuk</span>
                    <span class="btn-loader" style="display:none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
            </form>

            <div class="auth-footer">
                <p>Belum punya akun? <a href="/register">Daftar sekarang</a></p>
                <a href="/" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Home</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const errorMsg = document.getElementById('error-msg');
            errorMsg.textContent = '';
            setBtnLoading(btn, true);

            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        login: document.getElementById('login-input').value,
                        password: document.getElementById('password-input').value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showSuccess('Login berhasil! Mengalihkan...');
                    setTimeout(() => window.location.href = '/admin', 800);
                } else {
                    errorMsg.textContent = data.error || 'Login gagal.';
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
