<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | POIN.SISWA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #123146;
            --teal: #1F8A8A;
            --sage: #9CC9B3;
            --sky: #E7F2F4;
            --ink: #0E1E2A;
            --accent: #F2B56B;
            --card: #FFFFFF;
            --muted: #6C7A86;
            --danger: #C84646;
            --shadow: 0 20px 50px rgba(17, 35, 46, 0.15);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 600px at 10% 10%, #EAF6F4 0%, rgba(234, 246, 244, 0) 60%),
                radial-gradient(900px 500px at 90% 20%, #EAF0FF 0%, rgba(234, 240, 255, 0) 55%),
                linear-gradient(180deg, #F7FBFC 0%, #E9F2F6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 28px;
        }

        .brand-panel {
            background: linear-gradient(160deg, var(--navy) 0%, #0D2232 60%, #0A1A26 100%);
            color: #F6FBFC;
            border-radius: 24px;
            padding: 36px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            inset: auto -80px -80px auto;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(242, 181, 107, 0.5), rgba(242, 181, 107, 0));
            filter: blur(2px);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .logo-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2FB3B3, #9CC9B3);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #0D2C34;
        }

        .brand-title {
            margin: 20px 0 10px;
            font-size: 1.8rem;
            line-height: 1.2;
        }
        .brand-desc {
            margin: 0 0 24px;
            color: rgba(246, 251, 252, 0.8);
            font-size: 0.95rem;
        }

        .feature-list {
            display: grid;
            gap: 12px;
        }
        .feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.08);
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.9rem;
        }
        .feature span {
            display: inline-flex;
            width: 10px;
            height: 10px;
            margin-top: 6px;
            border-radius: 999px;
            background: var(--accent);
        }

        .login-card {
            background: var(--card);
            border-radius: 24px;
            padding: 32px;
            box-shadow: var(--shadow);
        }

        .login-card h2 {
            margin: 0 0 6px;
            font-size: 1.6rem;
        }
        .login-card p {
            margin: 0 0 22px;
            color: var(--muted);
        }

        .input-group {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
        }
        label {
            font-weight: 600;
            font-size: 0.9rem;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #D7E1E6;
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }
        input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(31, 138, 138, 0.15);
        }
        .password-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrap input {
            padding-right: 84px;
        }
        .toggle-pass {
            position: absolute;
            right: 10px;
            padding: 6px 10px;
            border: 1px solid #D7E1E6;
            background: #F7FBFC;
            color: var(--ink);
            border-radius: 10px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: background 0.2s ease, border 0.2s ease;
        }
        .toggle-pass:hover {
            background: #EAF2F4;
        }
        .eye-icon {
            width: 16px;
            height: 16px;
        }
        .error-text {
            display: none;
            font-size: 0.8rem;
            color: var(--danger);
        }
        .invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(200, 70, 70, 0.12);
        }

        .helper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--muted);
            margin: 8px 0 18px;
        }
        .helper strong {
            color: var(--ink);
        }

        .btn {
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--teal), #2FB3B3);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(31, 138, 138, 0.25);
        }

        .footnote {
            margin-top: 18px;
            font-size: 0.8rem;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 880px) {
            .shell {
                grid-template-columns: 1fr;
            }
            .brand-panel {
                order: 2;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="brand-panel">
            <div class="logo">
                <div class="logo-badge">PS</div>
                <div>POIN.SISWA</div>
            </div>
            <h1 class="brand-title">Sistem Disiplin Siswa yang rapi, aman, dan mudah dipakai.</h1>
            <p class="brand-desc">Kelola pelanggaran, pantau poin, dan siapkan laporan dengan tampilan yang jelas untuk seluruh pihak sekolah.</p>
            <div class="feature-list">
                <div class="feature"><span></span> Akses berbasis peran untuk admin, BK, pengajar, dan siswa</div>
                <div class="feature"><span></span> Pelaporan cepat dan dokumen cetak siap pakai</div>
                <div class="feature"><span></span> Rekap pelanggaran ringkas dan mudah dipahami</div>
            </div>
        </section>

        <section class="login-card">
            <h2>Masuk Akun</h2>
            <p>Gunakan akun sekolah kamu untuk masuk ke sistem.</p>
            <form action="process/login_process.php" method="post" id="loginForm" novalidate>
                <div class="input-group">
                    <label for="username">Username / NIS</label>
                    <input type="text" id="username" name="username" placeholder="contoh: 0021.001 atau 123456" required>
                    <div class="error-text" id="usernameError">Username atau NIS wajib diisi.</div>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
                            <span class="eye-label">Lihat</span>
                            <svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="3.5" fill="none" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                    <div class="error-text" id="passwordError">Password wajib diisi.</div>
                </div>
                <div class="helper">
                    <span>Butuh bantuan? Hubungi admin sekolah.</span>
                    <strong>Keamanan aktif</strong>
                </div>
                <button class="btn" type="submit">Masuk</button>
            </form>
            <div class="footnote">Dengan masuk, kamu setuju mengikuti kebijakan penggunaan sistem sekolah.</div>
        </section>
    </div>
    <script>
        const togglePass = document.getElementById('togglePass');
        const passwordInput = document.getElementById('password');
        const usernameInput = document.getElementById('username');
        const loginForm = document.getElementById('loginForm');
        const usernameError = document.getElementById('usernameError');
        const passwordError = document.getElementById('passwordError');
        togglePass.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            togglePass.querySelector('.eye-label').textContent = isHidden ? 'Sembunyikan' : 'Lihat';
            togglePass.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
        const validateField = (input, errorEl) => {
            if (!input.value.trim()) {
                input.classList.add('invalid');
                errorEl.style.display = 'block';
                return false;
            }
            input.classList.remove('invalid');
            errorEl.style.display = 'none';
            return true;
        };
        loginForm.addEventListener('submit', (e) => {
            const okUser = validateField(usernameInput, usernameError);
            const okPass = validateField(passwordInput, passwordError);
            if (!okUser || !okPass) {
                e.preventDefault();
            }
        });
        usernameInput.addEventListener('input', () => validateField(usernameInput, usernameError));
        passwordInput.addEventListener('input', () => validateField(passwordInput, passwordError));
    </script>
</body>
</html>
