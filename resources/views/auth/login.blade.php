<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login — Fried Chicken POS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red: #E53935;
            --red-dark: #C62828;
            --yellow: #FFC107;
        }

        * {
            margin: 0; padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        /* ─────────────────────────────────
           LOADING SCREEN
        ───────────────────────────────── */
        #loadingScreen {
            position: fixed;
            inset: 0;
            background: linear-gradient(160deg, #7f0000 0%, #E53935 60%, #ff6f60 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        #loadingScreen.hide {
            opacity: 0;
            visibility: hidden;
        }

        .ld-emoji {
            font-size: 5rem;
            animation: ldPulse 1.2s ease-in-out infinite;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,0.3));
            margin-bottom: 20px;
        }

        @keyframes ldPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        .ld-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .ld-sub {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1px;
            margin-bottom: 36px;
        }

        .ld-bar-wrap {
            width: 200px; height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .ld-bar {
            height: 100%;
            background: linear-gradient(90deg, #fff, #FFC107);
            border-radius: 4px;
            animation: ldBar 1.8s ease forwards;
        }

        @keyframes ldBar {
            0% { width: 0%; }
            40% { width: 50%; }
            70% { width: 80%; }
            100% { width: 100%; }
        }

        .ld-status {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.5);
        }

        /* ─────────────────────────────────
           BODY & WRAPPER
        ───────────────────────────────── */
        body {
            display: flex;
            min-height: 100vh;
            background: #f7f7f8;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        body.visible { opacity: 1; }

        /* ─────────────────────────────────
           LEFT PANEL — BRANDING (DESKTOP)
        ───────────────────────────────── */
        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, #7f0000 0%, #E53935 60%, #ff6f60 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -150px; left: -150px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(0,0,0,0.08);
            bottom: -80px; right: -80px;
        }

        .left-content { position: relative; z-index: 2; text-align: center; }

        .brand-emoji {
            font-size: 6rem;
            display: block;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 12px 32px rgba(0,0,0,0.3));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        .brand-title {
            font-size: 2.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 3px;
            line-height: 1.1;
        }

        .brand-title span { color: var(--yellow); }

        .brand-sub {
            color: rgba(255,255,255,0.65);
            margin-top: 10px;
            font-size: 0.88rem;
            letter-spacing: 1px;
        }

        .left-footer {
            position: absolute;
            bottom: 20px;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.3);
            z-index: 2;
        }

        /* ─────────────────────────────────
           RIGHT PANEL — FORM
        ───────────────────────────────── */
        .right-panel {
            width: 460px;
            min-width: 400px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
            position: relative;
            box-shadow: -24px 0 80px rgba(0,0,0,0.12);
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--red), var(--yellow), var(--red));
            background-size: 200%;
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 200%; }
            100% { background-position: -200%; }
        }

        .panel-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .panel-sub {
            font-size: 0.85rem;
            color: #aaa;
            margin-bottom: 32px;
        }

        /* ─────────────────────────────────
           FORM ELEMENTS
        ───────────────────────────────── */
        .field { margin-bottom: 18px; }

        .field label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .input-box { position: relative; }

        .input-box .ico {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: #ccc; font-size: 1rem;
            pointer-events: none;
        }

        .input-box input {
            width: 100%;
            padding: 13px 48px;
            border: 2px solid #efefef;
            border-radius: 12px;
            font-size: 0.92rem;
            color: #1a1a1a;
            background: #fafafa;
            outline: none;
            transition: all 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 48px;
        }

        .input-box input:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(229,57,53,0.08);
        }

        .input-box input::placeholder { color: #ccc; }

        .input-box .eye {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc; font-size: 1rem;
            padding: 4px;
            transition: color 0.2s;
            -webkit-tap-highlight-color: transparent;
        }

        .input-box .eye:hover { color: var(--red); }

        /* Remember Me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            min-height: 36px;
        }

        .remember-row input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--red);
            cursor: pointer;
            flex-shrink: 0;
        }

        .remember-row label {
            font-size: 0.85rem;
            color: #757575;
            cursor: pointer;
            user-select: none;
        }

        /* Submit Button */
        .btn-masuk {
            width: 100%;
            padding: 14px;
            min-height: 52px;
            background: linear-gradient(135deg, var(--red), var(--red-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.97rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 20px rgba(229,57,53,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-masuk:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(229,57,53,0.4);
        }

        .btn-masuk:active { transform: scale(0.98); }

        .btn-masuk:disabled {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none;
        }

        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Error */
        .alert-err {
            background: #FFF5F5;
            border: 2px solid #FFCDD2;
            border-radius: 10px;
            padding: 12px 14px;
            color: #C62828;
            font-size: 0.83rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        /* Footer */
        .form-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #f5f5f5;
        }

        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f5f5f5;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 0.72rem;
            color: #9e9e9e;
            font-weight: 600;
        }

        .version-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4CAF50;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ─────────────────────────────────
           TABLET (≤ 1024px)
        ───────────────────────────────── */
        @media (max-width: 1024px) {
            body {
                flex-direction: column;
                min-height: 100vh;
            }

            /* Branding pindah ke atas */
            .left-panel {
                width: 100%;
                flex: none;
                padding: 36px 24px 28px;
                min-height: auto;
            }

            .left-panel::before,
            .left-panel::after { display: none; }

            .brand-emoji {
                font-size: 5rem;
                margin-bottom: 14px;
            }

            .brand-title { font-size: 2.2rem; }
            .brand-sub { font-size: 0.85rem; margin-top: 8px; }
            .left-footer { display: none; }

            /* Form full width */
            .right-panel {
                width: 100%;
                min-width: unset;
                flex: 1;
                padding: 36px 32px 48px;
                box-shadow: none;
                justify-content: flex-start;
            }

            .panel-title { font-size: 1.6rem; }
            .panel-sub { margin-bottom: 28px; }

            /* Input lebih besar untuk tablet */
            .input-box input {
                min-height: 52px;
                font-size: 1rem;
                padding: 14px 52px;
                border-radius: 14px;
            }

            .input-box .ico { font-size: 1.1rem; left: 16px; }
            .input-box .eye { font-size: 1.1rem; right: 16px; }

            .remember-row {
                min-height: 44px;
                margin-bottom: 28px;
            }

            .remember-row input[type="checkbox"] {
                width: 20px; height: 20px;
            }

            .remember-row label { font-size: 0.92rem; }

            /* Tombol lebih besar */
            .btn-masuk {
                min-height: 56px;
                font-size: 1.05rem;
                border-radius: 14px;
            }

            .field { margin-bottom: 20px; }
            .field label { font-size: 0.78rem; margin-bottom: 8px; }
        }

        /* ─────────────────────────────────
           MOBILE (≤ 600px)
        ───────────────────────────────── */
        @media (max-width: 600px) {
            .left-panel { padding: 28px 20px 24px; }

            .brand-emoji { font-size: 3.5rem; margin-bottom: 10px; }
            .brand-title { font-size: 1.7rem; letter-spacing: 2px; }
            .brand-sub { font-size: 0.78rem; }

            .right-panel { padding: 28px 20px 40px; }
            .panel-title { font-size: 1.4rem; }

            .input-box input {
                min-height: 50px;
                font-size: 0.95rem;
            }

            .btn-masuk {
                min-height: 54px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Loading Screen -->
<div id="loadingScreen">
    <div class="ld-emoji">🍗</div>
    <div class="ld-title">FRIED CHICKEN POS</div>
    <div class="ld-sub">Sistem Kasir Digital</div>
    <div class="ld-bar-wrap"><div class="ld-bar"></div></div>
    <div class="ld-status" id="ldStatus">Memulai aplikasi...</div>
</div>

<!-- Left Panel -->
<div class="left-panel">
    <div class="left-content">
        <span class="brand-emoji">🍗</span>
        <div class="brand-title">FRIED<br><span>CHICKEN</span></div>
        <div class="brand-sub">Point of Sale System</div>
    </div>
    <div class="left-footer">© 2026 Fried Chicken POS v1.0</div>
</div>

<!-- Right Panel -->
<div class="right-panel">
    <div class="panel-title">Selamat Datang 👋</div>
    <div class="panel-sub">Masuk ke akun Anda untuk melanjutkan</div>

    @if($errors->any())
    <div class="alert-err">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
        @csrf

        <div class="field">
            <label for="username">Username</label>
            <div class="input-box">
                <i class="bi bi-person-fill ico"></i>
                <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Masukkan username"
                    value="{{ old('username') }}"
                    autocomplete="username"
                    autofocus
                    required
                >
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="input-box">
                <i class="bi bi-lock-fill ico"></i>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >
                <i class="bi bi-eye eye" id="eyeBtn" onclick="toggleEye()"></i>
            </div>
        </div>

        <div class="remember-row">
            <input
                type="checkbox"
                name="remember"
                id="remember"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember">Ingat saya selama 8 jam</label>
        </div>

        <button type="submit" class="btn-masuk" id="submitBtn">
            <div class="btn-spinner" id="btnSpinner"></div>
            <i class="bi bi-box-arrow-in-right" id="btnIcon"></i>
            <span id="btnText">Masuk</span>
        </button>
    </form>

    <div class="form-footer">
        <div class="version-badge">
            <div class="version-dot"></div>
            Fried Chicken POS v1.0 · {{ now()->format('Y') }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Loading screen
    const statuses = [
        'Memulai aplikasi...',
        'Memuat database...',
        'Menyiapkan sistem...',
        'Hampir siap...',
    ];
    let si = 0;
    const ldStatus = document.getElementById('ldStatus');
    const ldInterval = setInterval(() => {
        if (++si < statuses.length) ldStatus.textContent = statuses[si];
    }, 450);

    window.addEventListener('load', () => {
        setTimeout(() => {
            clearInterval(ldInterval);
            document.getElementById('loadingScreen').classList.add('hide');
            document.body.classList.add('visible');
        }, 1800);
    });

    // Toggle password
    function toggleEye() {
        const pwd = document.getElementById('password');
        const eye = document.getElementById('eyeBtn');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
        eye.className = pwd.type === 'password'
            ? 'bi bi-eye eye'
            : 'bi bi-eye-slash eye';
    }

    // Spinner saat submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn     = document.getElementById('submitBtn');
        const spinner = document.getElementById('btnSpinner');
        const icon    = document.getElementById('btnIcon');
        const text    = document.getElementById('btnText');
 
        btn.disabled           = true;
        spinner.style.display  = 'block';
        icon.style.display     = 'none';
        text.textContent       = 'Masuk...';
    });
</script>
</body>
</html>