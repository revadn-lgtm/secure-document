<?php

require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    // Mengamankan cookie session
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

$error = '';

// Pengaturan batas percobaan login
$maxAttempts = 5;
$lockDuration = 300; // 5 menit

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['login_lock_until'])) {
    $_SESSION['login_lock_until'] = 0;
}

// Membuat CSRF token
if (empty($_SESSION['login_csrf_token'])) {
    $_SESSION['login_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['login_csrf_token'], $submittedToken)
    ) {
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';

    } elseif (time() < $_SESSION['login_lock_until']) {

        $remaining = $_SESSION['login_lock_until'] - time();

        $error = 'Login dikunci sementara. Silakan coba lagi dalam '
            . ceil($remaining / 60) . ' menit.';

    } else {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare(
            "SELECT * FROM admins WHERE username = :username LIMIT 1"
        );

        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {

            // Login berhasil
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            // Menghapus penghitung percobaan login
            unset(
                $_SESSION['login_attempts'],
                $_SESSION['login_lock_until'],
                $_SESSION['login_csrf_token']
            );

            header('Location: admin/dashboard.php');
            exit;

        } else {

            // Menambah jumlah percobaan gagal
            $_SESSION['login_attempts']++;

            if ($_SESSION['login_attempts'] >= $maxAttempts) {

                $_SESSION['login_lock_until'] = time() + $lockDuration;
                $_SESSION['login_attempts'] = 0;

                $error = 'Terlalu banyak percobaan login. '
                    . 'Akun dikunci sementara selama 5 menit.';

            } else {

                $remainingAttempts = $maxAttempts - $_SESSION['login_attempts'];

                $error = 'Username atau password salah. Sisa percobaan: '
                    . $remainingAttempts . ' kali.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Secure Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --pink: #f8dce8;
            --green: #dcefe3;
            --yellow: #fff1c9;
            --blue: #dceafb;
            --ink: #39445b;
            --muted: #7b8495;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 30px 15px;
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, sans-serif;
            background:
                radial-gradient(circle at 12% 15%, rgba(248,220,232,.95), transparent 30%),
                radial-gradient(circle at 88% 18%, rgba(220,234,251,.95), transparent 32%),
                radial-gradient(circle at 15% 88%, rgba(220,239,227,.9), transparent 32%),
                radial-gradient(circle at 90% 85%, rgba(255,241,201,.95), transparent 30%),
                #fffafc;
        }

        .login-wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 3;
        }

        .login-card {
            border: 1px solid rgba(255,255,255,.9);
            border-radius: 28px;
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 55px rgba(91, 102, 130, .13);
            overflow: hidden;
        }

        .login-top {
            padding: 34px 35px 24px;
            text-align: center;
            background: linear-gradient(135deg, #fbe6ee, #e7f2ff 55%, #e8f5eb);
        }

        .brand-icon {
            width: 68px;
            height: 68px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: rgba(255,255,255,.8);
            color: #788db7;
            font-size: 30px;
            box-shadow: 0 8px 20px rgba(105, 120, 155, .1);
            margin-bottom: 15px;
        }

        .brand-title {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -.5px;
            margin: 0;
            color: #414d65;
        }

        .brand-subtitle {
            color: #788398;
            font-size: 13px;
            margin: 7px 0 0;
        }

        .login-content { padding: 30px 35px 32px; }

        .welcome-title {
            font-weight: 750;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .welcome-text {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 650;
            color: #515d73;
            margin-bottom: 8px;
        }

        .input-group-text {
            background: #f7f8fc;
            border: 1px solid #e8eaf1;
            border-right: 0;
            color: #8994aa;
            border-radius: 13px 0 0 13px;
            padding-left: 14px;
        }

        .form-control {
            min-height: 48px;
            background: #f7f8fc;
            border: 1px solid #e8eaf1;
            border-left: 0;
            border-radius: 0 13px 13px 0;
            color: var(--ink);
            font-size: 14px;
            box-shadow: none !important;
        }

        .form-control:focus {
            background: #fff;
            border-color: #c7d8f2;
        }

        .input-group:focus-within .input-group-text {
            background: #fff;
            border-color: #c7d8f2;
        }

        .login-button {
            min-height: 49px;
            border: 0;
            border-radius: 13px;
            color: #43516c;
            font-weight: 750;
            letter-spacing: .2px;
            background: linear-gradient(100deg, #f5dce8, #e0eafb 55%, #e1f0e4);
            box-shadow: 0 8px 18px rgba(125, 145, 180, .16);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .login-button:hover {
            color: #35425b;
            transform: translateY(-2px);
            box-shadow: 0 11px 22px rgba(125, 145, 180, .22);
        }

        .login-button:active { transform: translateY(0); }

        .alert {
            border: 0;
            border-radius: 12px;
            font-size: 13px;
        }

        .back-link {
            color: #7786a4;
            font-size: 13px;
            font-weight: 600;
        }

        .back-link:hover { color: #596d96; }

        .security-note {
            margin-top: 22px;
            padding-top: 17px;
            border-top: 1px solid #edf0f5;
            color: #929bad;
            text-align: center;
            font-size: 11px;
        }

        .paper-animation, .flower-animation {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .floating-paper {
            position: absolute;
            width: 34px;
            height: 43px;
            border-radius: 5px;
            opacity: .48;
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(180,190,210,.35);
            box-shadow: 0 5px 14px rgba(90,105,130,.08);
            animation: paperFloat 13s linear infinite;
        }

        .floating-paper::before,
        .floating-paper::after {
            content: "";
            position: absolute;
            left: 7px;
            height: 2px;
            border-radius: 5px;
            background: rgba(150,165,190,.35);
        }

        .floating-paper::before { top: 13px; width: 19px; }
        .floating-paper::after { top: 20px; width: 14px; }

        .paper-one { left: 8%; top: 105%; transform: rotate(-13deg); }
        .paper-two { left: 29%; top: 115%; animation-delay: -4s; transform: rotate(12deg); }
        .paper-three { left: 72%; top: 110%; animation-delay: -8s; transform: rotate(-8deg); }
        .paper-four { left: 91%; top: 120%; animation-delay: -2s; transform: rotate(17deg); }

        @keyframes paperFloat {
            0% { translate: 0 0; opacity: 0; }
            10% { opacity: .48; }
            90% { opacity: .4; }
            100% { translate: 35px -125vh; opacity: 0; }
        }

        .floating-flower {
            position: absolute;
            font-size: 27px;
            opacity: .55;
            animation: flowerFloat 16s ease-in-out infinite;
            filter: drop-shadow(0 4px 5px rgba(100,110,130,.08));
        }

        .flower-one { left: 10%; top: 18%; color: #e9a9c4; }
        .flower-two { left: 82%; top: 16%; color: #9fc9a9; animation-delay: -5s; }
        .flower-three { left: 17%; top: 75%; color: #e9c76f; animation-delay: -9s; }
        .flower-four { left: 88%; top: 72%; color: #9dbce5; animation-delay: -3s; }
        .flower-five { left: 50%; top: 8%; color: #e9a9c4; font-size: 20px; animation-delay: -7s; }
        .flower-six { left: 70%; top: 88%; color: #a9cfb1; font-size: 22px; animation-delay: -11s; }

        @keyframes flowerFloat {
            0%, 100% { transform: translateY(0) rotate(-12deg); }
            50% { transform: translateY(-22px) rotate(15deg); }
        }

        @media (max-width: 480px) {
            .login-top { padding: 28px 22px 21px; }
            .login-content { padding: 25px 22px 27px; }
            .brand-title { font-size: 22px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .floating-paper, .floating-flower { animation: none; }
            .login-button { transition: none; }
        }
    </style>
</head>

<body>

<div class="paper-animation" aria-hidden="true">
    <span class="floating-paper paper-one"></span>
    <span class="floating-paper paper-two"></span>
    <span class="floating-paper paper-three"></span>
    <span class="floating-paper paper-four"></span>
</div>

<div class="flower-animation" aria-hidden="true">
    <i class="bi bi-flower1 floating-flower flower-one"></i>
    <i class="bi bi-flower2 floating-flower flower-two"></i>
    <i class="bi bi-flower1 floating-flower flower-three"></i>
    <i class="bi bi-flower2 floating-flower flower-four"></i>
    <i class="bi bi-flower1 floating-flower flower-five"></i>
    <i class="bi bi-flower2 floating-flower flower-six"></i>
</div>

<main class="login-wrapper">
    <div class="card login-card">
        <div class="login-top">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="brand-title">SecureDocument</h1>
            <p class="brand-subtitle">Sistem Informasi Pengelolaan Dokumen Terenkripsi</p>
        </div>

        <div class="login-content">
            <div class="text-center">
                <h2 class="welcome-title">Selamat Datang</h2>
                <p class="welcome-text">Silakan login untuk melanjutkan ke halaman administrator.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-exclamation-circle-fill mt-1"></i>
                    <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['login_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn login-button w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Administrator
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none back-link">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman utama
                </a>
            </div>

            <div class="security-note">
                <i class="bi bi-shield-check me-1"></i>
                Akses terbatas untuk administrator yang berwenang
            </div>
        </div>
    </div>
</main>

</body>
</html>
