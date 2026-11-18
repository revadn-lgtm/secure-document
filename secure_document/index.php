
<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document | Data Protection System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #08111f;
            color: #f1f5f9;
        }

        .navbar {
            background: rgba(8, 17, 31, 0.92);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 18px 0;
        }

        .brand {
            color: white;
            text-decoration: none;
            font-weight: 800;
            font-size: 21px;
        }

        .brand span {
            color: #38bdf8;
        }

        .nav-link {
            color: #aab8ca;
            margin-right: 25px;
        }

        .nav-link:hover {
            color: white;
        }

        .btn-login {
            background: #0ea5e9;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 11px 22px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #0284c7;
            color: white;
        }

        .hero {
            min-height: 610px;
            display: flex;
            align-items: center;
            background:
                radial-gradient(circle at 80% 30%, rgba(14,165,233,0.15), transparent 35%),
                linear-gradient(135deg, #08111f, #0b1c32);
            padding: 90px 0;
        }

        .badge-security {
            display: inline-block;
            color: #7dd3fc;
            background: rgba(14,165,233,0.1);
            border: 1px solid rgba(14,165,233,0.25);
            padding: 9px 15px;
            border-radius: 30px;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .hero h1 {
            font-size: clamp(38px, 5vw, 62px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 25px;
        }

        .hero h1 span {
            color: #38bdf8;
        }

        .hero p {
            color: #aab8ca;
            font-size: 16px;
            line-height: 1.9;
            max-width: 560px;
        }

        .btn-primary-custom {
            display: inline-block;
            background: #0ea5e9;
            color: white;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 10px;
            margin-top: 25px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary-custom:hover {
            background: #0284c7;
            transform: translateY(-2px);
            color: white;
        }

        .security-card {
            background: rgba(15, 30, 50, 0.85);
            border: 1px solid rgba(148,163,184,0.15);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.25);
        }

        .lock-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(14,165,233,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin: 0 auto 20px;
        }

        .encrypted-box {
            background: #08111f;
            border: 1px solid rgba(56,189,248,0.15);
            padding: 17px;
            border-radius: 12px;
            margin-top: 20px;
        }

        .encrypted-box small {
            color: #64748b;
        }

        .encrypted-text {
            color: #38bdf8;
            font-size: 13px;
            word-break: break-all;
            margin-top: 8px;
        }

        .status {
            color: #4ade80;
            font-size: 13px;
            margin-top: 15px;
        }

        .features {
            padding: 90px 0;
            background: #0b1728;
        }

        .section-title {
            font-weight: 800;
            font-size: 36px;
            margin-bottom: 15px;
        }

        .section-description {
            color: #94a3b8;
            max-width: 650px;
            margin: auto;
            line-height: 1.8;
        }

        .feature-card {
            background: #101f33;
            border: 1px solid rgba(255,255,255,0.07);
            padding: 27px;
            border-radius: 16px;
            height: 100%;
            transition: 0.2s;
        }

        .feature-card:hover {
            border-color: rgba(56,189,248,0.4);
            transform: translateY(-4px);
        }

        .feature-icon {
            font-size: 28px;
            margin-bottom: 18px;
        }

        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #94a3b8;
            line-height: 1.8;
            font-size: 14px;
            margin: 0;
        }

        footer {
            background: #060d18;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 25px 0;
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 991px) {
            .hero {
                padding: 65px 0;
            }

            .hero h1 {
                font-size: 42px;
            }

            .security-card {
                margin-top: 45px;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a href="index.php" class="brand">
            Secure<span>Document.</span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="#features" class="nav-link d-none d-md-block">Fitur Keamanan</a>
            <a href="login.php" class="btn-login">Admin Login ↗</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7">
                <div class="badge-security">
                    🔒 Secure Data Management System
                </div>

                <h1>
                    Protect Your Data.<br>
                    <span>Secure Your Documents.</span>
                </h1>

                <p>
                    Secure Document merupakan sistem informasi pengelolaan
                    dokumen digital yang dirancang untuk menjaga kerahasiaan
                    data melalui teknologi enkripsi AES-256 dan RSA.
                    Dokumen disimpan dalam bentuk terenkripsi untuk
                    meningkatkan keamanan informasi.
                </p>

                <a href="login.php" class="btn-primary-custom">
                    Masuk Administrator &nbsp; →
                </a>
            </div>

            <!-- SECURITY PREVIEW -->
            <div class="col-lg-5">
                <div class="security-card text-center">
                    <div class="lock-icon">🔐</div>

                    <h5 class="fw-bold">Encrypted Document</h5>
                    <p class="text-secondary small">
                        Data protection is active
                    </p>

                    <div class="encrypted-box text-start">
                        <small>ENCRYPTED DATA PREVIEW</small>

                        <div class="encrypted-text">
                            U2FsdGVkX19x8K7mA9zP2vL0xY4...
                        </div>
                    </div>

                    <div class="status">
                        ● Data Protection Enabled
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">Security Features</h2>

            <p class="section-description">
                Perlindungan dokumen digital menggunakan kombinasi
                metode kriptografi untuk menjaga kerahasiaan
                dan keamanan data yang disimpan.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h5>AES-256 Encryption</h5>
                    <p>
                        Isi dokumen dienkripsi menggunakan algoritma
                        AES-256 sehingga data tidak disimpan dalam
                        bentuk teks asli.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">🔑</div>
                    <h5>RSA Key Protection</h5>
                    <p>
                        Kunci AES diamankan menggunakan RSA
                        Public Key dan dibuka kembali menggunakan
                        RSA Private Key.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">📁</div>
                    <h5>Secure Storage</h5>
                    <p>
                        Dokumen disimpan dalam database dalam
                        bentuk ciphertext untuk menjaga
                        kerahasiaan informasi.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container text-center">
        Secure Document &copy; 2026 | Data Encryption & Information Security
    </div>
</footer>

</body>
</html>
