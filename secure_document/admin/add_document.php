
<?php

session_start();

require_once '../config/database.php';
require_once '../includes/crypto.php';
require_once '../includes/auth.php';

// Memastikan admin sudah login
requireLogin();

// Membuat CSRF token jika belum tersedia
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Memeriksa CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['csrf_token'], $submittedToken)
    ) {
        $message = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } else {

        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $category === '' || $content === '') {
            $message = 'Semua kolom wajib diisi.';
        } else {

            try {
                // Mengenkripsi isi dokumen menggunakan AES dan RSA
                $encrypted = encryptDocument($content);

                // Menyimpan dokumen terenkripsi ke database
                $sql = "INSERT INTO documents
                        (title, category, ciphertext, iv, auth_tag, encrypted_aes_key)
                        VALUES
                        (:title, :category, :ciphertext, :iv, :auth_tag, :encrypted_aes_key)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':title' => $title,
                    ':category' => $category,
                    ':ciphertext' => $encrypted['ciphertext'],
                    ':iv' => $encrypted['iv'],
                    ':auth_tag' => $encrypted['auth_tag'],
                    ':encrypted_aes_key' => $encrypted['encrypted_aes_key']
                ]);

                // Mengganti token setelah berhasil menyimpan
                unset($_SESSION['csrf_token']);

                header('Location: dashboard.php?status=success');
                exit;

            } catch (Throwable $e) {
                error_log($e->getMessage());
                $message = 'Gagal menyimpan dokumen. Silakan coba kembali.';
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
    <title>Tambah Dokumen - Secure Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm mx-auto" style="max-width: 750px;">

        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0">Tambah Dokumen Baru</h4>
        </div>

        <div class="card-body p-4">

            <?php if ($message !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <!-- CSRF Token -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="mb-3">
                    <label class="form-label">Judul Dokumen</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Masukkan judul dokumen"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="">Pilih kategori</option>
                        <option value="Surat">Surat</option>
                        <option value="Laporan">Laporan</option>
                        <option value="Arsip">Arsip</option>
                        <option value="Pribadi">Pribadi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi Dokumen</label>
                    <textarea
                        name="content"
                        class="form-control"
                        rows="8"
                        placeholder="Masukkan isi dokumen yang akan dienkripsi"
                        required
                    ></textarea>
                </div>

                <div class="alert alert-info">
                    <strong>Keamanan:</strong>
                    Isi dokumen akan dienkripsi menggunakan AES-256-GCM
                    sebelum disimpan ke database. Permintaan formulir juga
                    dilindungi menggunakan CSRF token.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Enkripsi dan Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
