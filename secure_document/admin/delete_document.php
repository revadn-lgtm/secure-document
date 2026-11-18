
<?php

require_once '../config/database.php';
require_once '../includes/auth.php';

// Memastikan admin sudah login
requireLogin();

// Header keamanan
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode permintaan tidak diizinkan.');
}

// Mengambil ID dan CSRF token
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$token = $_POST['csrf_token'] ?? '';

// Memvalidasi ID dan CSRF token
if (
    $id === false ||
    $id === null ||
    $id < 1 ||
    !is_string($token) ||
    empty($_SESSION['delete_token']) ||
    !hash_equals($_SESSION['delete_token'], $token)
) {
    http_response_code(403);
    exit('Permintaan penghapusan tidak valid.');
}

try {

    // Menghapus dokumen menggunakan prepared statement
    $stmt = $pdo->prepare(
        "DELETE FROM documents WHERE id = :id"
    );

    $stmt->execute([
        ':id' => $id
    ]);

    // Menghapus token setelah digunakan
    unset($_SESSION['delete_token']);

    if ($stmt->rowCount() > 0) {
        header('Location: dashboard.php?status=deleted');
        exit;
    }

    http_response_code(404);
    exit('Dokumen tidak ditemukan atau sudah dihapus.');

} catch (PDOException $e) {

    error_log('Gagal menghapus dokumen: ' . $e->getMessage());

    http_response_code(500);
    exit('Terjadi kesalahan saat menghapus dokumen.');

}
