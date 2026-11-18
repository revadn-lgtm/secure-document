
<?php

require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/crypto.php';

// Memastikan admin sudah login
requireLogin();

// Header keamanan
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Memvalidasi ID dokumen
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('ID dokumen tidak valid.');
}

// Mengambil dokumen menggunakan prepared statement
$stmt = $pdo->prepare(
    "SELECT * FROM documents WHERE id = :id LIMIT 1"
);

$stmt->execute([':id' => $id]);

$document = $stmt->fetch();

if (!$document) {
    http_response_code(404);
    exit('Dokumen tidak ditemukan.');
}

$error = '';
$content = '';

try {

    // Mendekripsi isi dokumen
    $content = decryptDocument($document);

} catch (Throwable $e) {

    // Mencatat kesalahan ke log server
    error_log('Dekripsi dokumen ID ' . $id . ' gagal: ' . $e->getMessage());

    // Pesan umum untuk pengguna
    $error = 'Dokumen gagal didekripsi. Periksa ketersediaan kunci RSA dan integritas data.';

}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lihat Dokumen - Secure Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
        }

        .document-card {
            border: none;
            border-radius: 14px;
        }

        .document-content {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            min-height: 180px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .ciphertext {
            font-family: monospace;
            font-size: 13px;
        }
    </style>

</head>

<body>

<div class="container py-5">

    <div class="card document-card shadow-sm mx-auto" style="max-width: 850px;">

        <div class="card-header bg-primary text-white p-3">
            <h4 class="mb-0">Detail Dokumen</h4>
        </div>

        <div class="card-body p-4">

            <h5 class="fw-bold">
                <?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8') ?>
            </h5>

            <p class="text-muted mb-4">
                Kategori:
                <?= htmlspecialchars($document['category'], ENT_QUOTES, 'UTF-8') ?>

                <br>

                Tanggal:
                <?= htmlspecialchars($document['created_at'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <?php if ($error !== ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php else: ?>

                <div class="alert alert-success">
                    Dokumen berhasil didekripsi menggunakan AES-256-GCM
                    dengan kunci AES yang dilindungi RSA.
                </div>

                <h6 class="fw-bold mb-3">Isi Dokumen</h6>

                <div class="document-content mb-4"><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></div>

                <h6 class="fw-bold">Data Ciphertext</h6>

                <div class="document-content ciphertext mb-3"><?= htmlspecialchars($document['ciphertext'], ENT_QUOTES, 'UTF-8') ?></div>

                <p class="text-muted small">
                    Data di atas merupakan ciphertext yang tersimpan
                    di database. Isi dokumen asli hanya dapat ditampilkan
                    setelah proses dekripsi berhasil.
                </p>

            <?php endif; ?>

            <a href="dashboard.php" class="btn btn-secondary mt-3">
                Kembali ke Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>
