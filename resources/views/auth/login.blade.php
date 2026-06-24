<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login — Fried Chicken POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif; }
        body { min-height:100vh; display:flex; background:#f7f7f8; }

        /* LEFT */
        .left {
            flex:1;
            background:linear-gradient(160deg,#7f0000 0%,#E53935 60%,#ff6f60 100%);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:48px; position:relative; overflow:hidden;
        }
        .left::before {
            content:''; position:absolute; width:500px; height:500px; border-radius:50%;
            background:rgba(255,255,255,0.05); top:-150px; left:-150px;
        }
        .left::after {
            content:''; position:absolute; width:300px; height:300px; border-radius:50%;
            background:rgba(0,0,0,0.08); bottom:-80px; right:-80px;
        }
        .left-content { position:relative; z-index:2; text-align:center; }
        .chicken {
            font-size:6rem; display:block; margin-bottom:24px;
            animation:float 3s ease-in-out infinite;
            filter:drop-shadow(0 12px 32px rgba(0,0,0,0.3));
        }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
        .left h1 { font-size:2.8rem; font-weight:800; color:#fff; letter-spacing:3px; line-height:1.1; }
        .left h1 span { color:#FFC107; }
        .left p { color:rgba(255,255,255,0.65); margin-top:10px; font-size:0.9rem; letter-spacing:1px; }
        .left-footer { position:absolute; bottom:20px; font-size:0.7rem; color:rgba(255,255,255,0.3); z-index:2; }

        /* RIGHT */
        .right {
            width:460px; min-width:400px; background:#fff;
            display:flex; flex-direction:column; justify-content:center;
            padding:52px 44px; position:relative;
            box-shadow:-24px 0 80px rgba(0,0,0,0.12);
        }
        .right::before {
            content:''; position:absolute; top:0; left:0; right:0; height:4px;
            background:linear-gradient(90deg,#E53935,#FFC107,#E53935);
            background-size:200%; animation:bar 3s linear infinite;
        }
        @keyframes bar { 0%{background-position:200%} 100%{background-position:-200%} }

        .right h2 { font-size:1.7rem; font-weight:800; color:#1a1a1a; margin-bottom:6px; }
        .right .sub { font-size:0.85rem; color:#aaa; margin-bottom:36px; }

        .field { margin-bottom:20px; }
        .field label {
            display:block; font-size:0.75rem; font-weight:700; color:#555;
            text-transform:uppercase; letter-spacing:0.8px; margin-bottom:8px;
        }
        .input-box { position:relative; }
        .input-box i.ico { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#ccc; font-size:0.95rem; }
        .input-box input {
            width:100%; padding:13px 44px; border:2px solid #efefef; border-radius:12px;
            font-size:0.92rem; color:#1a1a1a; background:#fafafa; outline:none;
            transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
        }
        .input-box input:focus { border-color:#E53935; background:#fff; box-shadow:0 0 0 4px rgba(229,57,53,0.08); }
        .input-box input::placeholder { color:#ccc; }
        .input-box .eye { position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#ccc; }
        .input-box .eye:hover { color:#E53935; }

        .btn {
            width:100%; padding:14px; background:#E53935; color:#fff; border:none;
            border-radius:12px; font-size:0.95rem; font-weight:700; cursor:pointer;
            margin-top:28px; transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
            box-shadow:0 4px 20px rgba(229,57,53,0.3);
        }
        .btn:hover { background:#c62828; transform:translateY(-1px); box-shadow:0 8px 28px rgba(229,57,53,0.4); }
        .btn:active { transform:translateY(0); }

        .error {
            background:#FFF5F5; border:2px solid #FFCDD2; border-radius:10px;
            padding:12px 14px; color:#C62828; font-size:0.83rem;
            display:flex; align-items:center; gap:8px; margin-bottom:20px;
        }

        .footer { text-align:center; margin-top:24px; font-size:0.72rem; color:#ccc; }

        @media (max-width:768px) {
            .left { display:none; }
            .right { width:100%; min-width:unset; padding:32px 24px; box-shadow:none; }
        }
    </style>
</head>
<body>

<!-- LEFT -->
<div class="left">
    <div class="left-content">
        <span class="chicken">🍗</span>
        <h1>FRIED<br><span>CHICKEN</span></h1>
        <p>Point of Sale System</p>
    </div>
    <div class="left-footer">© 2026 Fried Chicken POS v1.0</div>
</div>

<!-- RIGHT -->
<div class="right">
    <h2>Selamat Datang 👋</h2>
    <p class="sub">Masuk ke akun Anda untuk melanjutkan</p>

    @if($errors->any())
    <div class="error">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="field">
            <label>Username</label>
            <div class="input-box">
                <i class="bi bi-person-fill ico"></i>
                <input type="text" name="username" placeholder="Masukkan username"
                    value="{{ old('username') }}" autocomplete="username" autofocus>
            </div>
        </div>
        <div class="field">
            <label>Password</label>
            <div class="input-box">
                <i class="bi bi-lock-fill ico"></i>
                <input type="password" name="password" id="pwd" placeholder="Masukkan password">
                <i class="bi bi-eye eye" id="eyeBtn" onclick="toggleEye()"></i>
            </div>
        </div>
        <button type="submit" class="btn">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <div class="footer">© 2026 Fried Chicken POS</div>
</div>

<script>
function toggleEye() {
    const p = document.getElementById('pwd');
    const e = document.getElementById('eyeBtn');
    p.type = p.type === 'password' ? 'text' : 'password';
    e.className = p.type === 'password' ? 'bi bi-eye eye' : 'bi bi-eye-slash eye';
}
</script>
</body>
</html>
