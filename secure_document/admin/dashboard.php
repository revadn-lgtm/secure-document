
<?php

require_once '../config/database.php';
require_once '../includes/auth.php';

// Memastikan admin sudah login
requireLogin();

// Membuat token keamanan untuk penghapusan dokumen
if (empty($_SESSION['delete_token'])) {
    $_SESSION['delete_token'] = bin2hex(random_bytes(32));
}

// Header keamanan
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Mengambil data dokumen
$stmt = $pdo->query(
    "SELECT id, title, category, created_at
     FROM documents
     ORDER BY id DESC"
);

$documents = $stmt->fetchAll();
$totalDocuments = count($documents);

// Notifikasi
$status = $_GET['status'] ?? '';
$notification = '';
$notificationType = 'success';

if ($status === 'success') {
    $notification = 'Dokumen berhasil dienkripsi dan disimpan.';
} elseif ($status === 'deleted') {
    $notification = 'Dokumen berhasil dihapus.';
} elseif ($status !== '') {
    $notification = 'Status permintaan tidak dikenali.';
    $notificationType = 'warning';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Secure Document</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    body {
        background: linear-gradient(135deg, #fff5f8, #f0f7ff, #f3fff5);
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #46536a;
        overflow-x: hidden;
    }

    .top-navbar {
        background: rgba(255, 255, 255, 0.94);
        padding: 14px 30px;
        box-shadow: 0 3px 15px rgba(120, 145, 180, 0.12);
        border-bottom: 1px solid #e8edf5;
        position: relative;
        z-index: 5;
    }

    .brand {
        color: #647da5;
        font-size: 22px;
        font-weight: bold;
        text-decoration: none;
        letter-spacing: 0.3px;
    }

    .brand i {
        color: #8db9e8;
        margin-right: 8px;
    }

    .nav-icons {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        color: #71839c;
        font-size: 21px;
        text-decoration: none;
        background: #f0f5fc;
        transition: 0.3s;
    }

    .nav-icon:hover {
        background: #dceafa;
        color: #527eae;
        transform: translateY(-4px);
    }

    .nav-icon.active {
        background: #dceafa;
        color: #527eae;
    }

    .nav-icon.logout {
        background: #fde8ed;
        color: #c47c91;
    }

    .nav-icon.logout:hover {
        background: #f8d5df;
        color: #a95773;
    }

    .main-content {
        padding: 35px;
        max-width: 1500px;
        margin: auto;
        position: relative;
        z-index: 2;
    }

    .page-heading {
        margin-bottom: 28px;
    }

    .page-heading h3 {
        color: #627a9e;
    }

    .page-heading p {
        color: #8b99aa;
    }

    .stat-card {
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: 18px;
        transition: 0.3s;
        overflow: hidden;
        background: rgba(255,255,255,0.9);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(140, 160, 190, 0.16) !important;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        font-size: 23px;
    }

    .icon-blue {
        background: #dceafa;
        color: #6595c7;
    }

    .icon-purple {
        background: #f9dfeb;
        color: #c17e9e;
    }

    .icon-green {
        background: #dff2df;
        color: #72a77b;
    }

    .table-card {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        background: rgba(255,255,255,0.95);
    }

    .table thead th {
        background: #eaf2fc;
        color: #7085a3;
        font-size: 13px;
        font-weight: 600;
        padding: 16px;
        white-space: nowrap;
        border: none;
    }

    .table tbody td {
        padding: 16px;
        color: #66758b;
        border-color: #f0f2f7;
    }

    .table tbody tr:hover {
        background: #fff8fb;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 11px;
        font-size: 16px;
        text-decoration: none;
        transition: 0.2s;
    }

    .view-btn {
        background: #dceafa;
        color: #648dbb;
    }

    .view-btn:hover {
        background: #bdd8f5;
        color: #416e9e;
    }

    .delete-btn {
        background: #fde3e9;
        color: #c47d91;
    }

    .delete-btn:hover {
        background: #f6cbd7;
        color: #a95773;
    }

    .status-badge {
        background: #e0f2e1;
        color: #689773;
        padding: 7px 11px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .section-title {
        font-weight: bold;
        color: #687e9e;
    }

    .modal-content {
        border: none;
        border-radius: 18px;
    }

    .modal-icon {
        width: 58px;
        height: 58px;
        background: #fde3e9;
        color: #c47d91;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        margin: 0 auto 15px;
    }

    footer {
        color: #9aa8ba;
        font-size: 13px;
    }

    /* Kertas dekoratif yang bergerak di latar belakang */
    .paper-animation {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .floating-paper {
        position: absolute;
        bottom: -100px;
        opacity: 0.20;
        animation: paperFloat linear infinite;
        filter: drop-shadow(0 4px 5px rgba(120, 145, 180, 0.12));
    }

    .paper-1 {
        left: 8%;
        color: #e99db8;
        font-size: 30px;
        animation-duration: 17s;
    }

    .paper-2 {
        left: 25%;
        color: #8eb99b;
        font-size: 24px;
        animation-duration: 22s;
        animation-delay: 3s;
    }

    .paper-3 {
        left: 47%;
        color: #e9c66d;
        font-size: 32px;
        animation-duration: 19s;
        animation-delay: 6s;
    }

    .paper-4 {
        left: 70%;
        color: #8eb9e5;
        font-size: 26px;
        animation-duration: 24s;
        animation-delay: 2s;
    }

    .paper-5 {
        left: 88%;
        color: #e99db8;
        font-size: 29px;
        animation-duration: 20s;
        animation-delay: 8s;
    }

    @keyframes paperFloat {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 0;
        }
        15% {
            opacity: 0.22;
        }
        50% {
            transform: translateY(-55vh) translateX(35px) rotate(150deg);
        }
        85% {
            opacity: 0.18;
        }
        100% {
            transform: translateY(-115vh) translateX(-35px) rotate(330deg);
            opacity: 0;
        }
    }


    /* Bunga pastel yang melayang di latar belakang */
    .flower-animation {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .floating-flower {
        position: absolute;
        bottom: -90px;
        opacity: 0;
        animation: flowerFloat linear infinite;
        filter: drop-shadow(0 4px 5px rgba(150, 130, 160, 0.12));
    }

    .flower-1 {
        left: 13%;
        color: #e8a9c2;
        font-size: 27px;
        animation-duration: 19s;
    }

    .flower-2 {
        left: 34%;
        color: #a9cdae;
        font-size: 22px;
        animation-duration: 23s;
        animation-delay: 4s;
    }

    .flower-3 {
        left: 58%;
        color: #e8cf86;
        font-size: 30px;
        animation-duration: 21s;
        animation-delay: 7s;
    }

    .flower-4 {
        left: 79%;
        color: #a8c9e9;
        font-size: 25px;
        animation-duration: 25s;
        animation-delay: 2s;
    }

    .flower-5 {
        left: 92%;
        color: #e8a9c2;
        font-size: 21px;
        animation-duration: 20s;
        animation-delay: 10s;
    }

    @keyframes flowerFloat {
        0% {
            transform: translateY(0) translateX(0) rotate(0deg);
            opacity: 0;
        }
        12% {
            opacity: 0.30;
        }
        50% {
            transform: translateY(-55vh) translateX(28px) rotate(150deg);
        }
        88% {
            opacity: 0.24;
        }
        100% {
            transform: translateY(-115vh) translateX(-25px) rotate(320deg);
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .floating-flower {
            animation: none;
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .floating-paper {
            animation: none;
            display: none;
        }
    }

    @media (max-width: 576px) {
        .top-navbar {
            padding: 12px 15px;
        }

        .brand {
            font-size: 18px;
        }

        .nav-icons {
            gap: 5px;
        }

        .nav-icon {
            width: 37px;
            height: 37px;
            font-size: 19px;
        }

        .main-content {
            padding: 22px 15px;
        }

        .page-heading h3 {
            font-size: 23px;
        }
    }
</style>
</head>

<body>


<div class="flower-animation" aria-hidden="true">
    <i class="bi bi-flower1 floating-flower flower-1"></i>
    <i class="bi bi-flower2 floating-flower flower-2"></i>
    <i class="bi bi-flower1 floating-flower flower-3"></i>
    <i class="bi bi-flower2 floating-flower flower-4"></i>
    <i class="bi bi-flower1 floating-flower flower-5"></i>
</div>

<div class="paper-animation" aria-hidden="true">
    <i class="bi bi-file-earmark-text-fill floating-paper paper-1"></i>
    <i class="bi bi-file-earmark-fill floating-paper paper-2"></i>
    <i class="bi bi-file-earmark-text floating-paper paper-3"></i>
    <i class="bi bi-file-earmark-fill floating-paper paper-4"></i>
    <i class="bi bi-file-earmark-text-fill floating-paper paper-5"></i>
</div>

<!-- Navbar Atas -->
<nav class="top-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Logo -->
        <a href="dashboard.php" class="brand">
            <i class="bi bi-shield-lock-fill"></i>SecureDocument
        </a>

        <!-- Menu Ikon -->
        <div class="nav-icons">

            <a href="dashboard.php"
               class="nav-icon active"
               data-bs-toggle="tooltip"
               data-bs-placement="bottom"
               title="Dashboard"
               aria-label="Dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
            </a>

            <a href="add_document.php"
               class="nav-icon"
               data-bs-toggle="tooltip"
               data-bs-placement="bottom"
               title="Tambah Dokumen"
               aria-label="Tambah Dokumen">
                <i class="bi bi-file-earmark-plus-fill"></i>
            </a>

            <a href="../logout.php"
               class="nav-icon logout"
               data-bs-toggle="tooltip"
               data-bs-placement="bottom"
               title="Logout"
               aria-label="Logout"
               onclick="return confirm('Apakah kamu yakin ingin logout?')">
                <i class="bi bi-box-arrow-right"></i>
            </a>

        </div>

    </div>
</nav>


<!-- Konten Dashboard -->
<div class="main-content">

    <!-- Judul Halaman -->
    <div class="page-heading">

        <h3 class="fw-bold mb-2">
            Dashboard Admin
        </h3>

        <p class="mb-0">
            Selamat datang,
            <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>.
            Berikut ringkasan pengelolaan dokumen kamu.
        </p>

    </div>


    <!-- Notifikasi -->
    <?php if ($notification !== ''): ?>

        <div class="alert alert-<?= $notificationType ?> alert-dismissible fade show"
             role="alert">

            <?= htmlspecialchars($notification, ENT_QUOTES, 'UTF-8') ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup">
            </button>

        </div>

    <?php endif; ?>


    <!-- Statistik -->
    <div class="row g-4 mb-4">

        <!-- Total Dokumen -->
        <div class="col-md-4">

            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-2">
                                Total Dokumen
                            </p>

                            <h2 class="fw-bold mb-0">
                                <?= $totalDocuments ?>
                            </h2>
                        </div>

                        <div class="stat-icon icon-blue">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>


        <!-- Metode Enkripsi -->
        <div class="col-md-4">

            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-2">
                                Metode Enkripsi
                            </p>

                            <h5 class="fw-bold mb-0">
                                AES-256-GCM
                            </h5>
                        </div>

                        <div class="stat-icon icon-purple">
                            <i class="bi bi-lock-fill"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>


        <!-- RSA -->
        <div class="col-md-4">

            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-2">
                                Pengamanan Kunci
                            </p>

                            <h5 class="fw-bold mb-0">
                                RSA 2048-bit
                            </h5>
                        </div>

                        <div class="stat-icon icon-green">
                            <i class="bi bi-shield-check"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>


    <!-- Daftar Dokumen -->
    <div class="card table-card shadow-sm">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <div>
                    <h5 class="section-title mb-1">
                        <i class="bi bi-folder2-open me-2 text-primary"></i>
                        Daftar Dokumen
                    </h5>

                    <small class="text-muted">
                        Dokumen yang telah disimpan dalam sistem.
                    </small>
                </div>

                <span class="status-badge">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    Sistem Enkripsi Aktif
                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul Dokumen</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($documents)): ?>

                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">

                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                    Belum ada dokumen yang disimpan.

                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($documents as $index => $document): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td class="fw-semibold">

                                        <i class="bi bi-file-earmark-lock text-primary me-2"></i>

                                        <?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8') ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($document['category'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date('d-m-Y H:i', strtotime($document['created_at'])),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="status-badge">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Terenkripsi
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        <!-- Tombol Lihat -->
                                        <a href="view_document.php?id=<?= (int) $document['id'] ?>"
                                           class="action-btn view-btn me-1"
                                           data-bs-toggle="tooltip"
                                           title="Lihat Dokumen"
                                           aria-label="Lihat Dokumen">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>


                                        <!-- Tombol Hapus -->
                                        <button type="button"
                                                class="action-btn delete-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= (int) $document['id'] ?>"
                                                data-title="<?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8') ?>"
                                                aria-label="Hapus Dokumen"
                                                title="Hapus Dokumen">

                                            <i class="bi bi-trash3-fill"></i>

                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- Footer -->
    <footer class="text-center mt-4">

        <i class="bi bi-shield-lock me-1"></i>

        &copy; <?= date('Y') ?> Secure Document.
        Sistem Pengelolaan Dokumen Terenkripsi.

    </footer>

</div>


<!-- Modal Konfirmasi Hapus -->
<div class="modal fade"
     id="deleteModal"
     tabindex="-1"
     aria-labelledby="deleteModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-body text-center p-4">

                <div class="modal-icon">
                    <i class="bi bi-trash3-fill"></i>
                </div>

                <h5 class="fw-bold mb-2" id="deleteModalLabel">
                    Konfirmasi Penghapusan
                </h5>

                <p class="text-muted mb-2">
                    Apakah kamu yakin ingin menghapus dokumen ini?
                </p>

                <p class="fw-bold text-dark mb-4"
                   id="deleteDocumentTitle">
                </p>


                <div class="d-flex justify-content-center gap-2">

                    <button type="button"
                            class="btn btn-secondary px-4"
                            data-bs-dismiss="modal">

                        Batal

                    </button>


                    <!-- Form Penghapusan POST -->
                    <form action="delete_document.php"
                          method="POST"
                          id="deleteForm">

                        <input type="hidden"
                               name="id"
                               id="deleteDocumentId">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= htmlspecialchars($_SESSION['delete_token'], ENT_QUOTES, 'UTF-8') ?>">

                        <button type="submit"
                                class="btn btn-danger px-4">

                            <i class="bi bi-trash3 me-1"></i>
                            Ya, Hapus

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

    // Mengaktifkan tooltip pada ikon menu dan tombol
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');

    tooltipElements.forEach(function (element) {
        new bootstrap.Tooltip(element);
    });


    // Mengambil data dokumen yang dipilih
    const deleteModal = document.getElementById('deleteModal');

    deleteModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        const documentId = button.getAttribute('data-id');
        const documentTitle = button.getAttribute('data-title');

        document.getElementById('deleteDocumentId').value = documentId;

        document.getElementById('deleteDocumentTitle').textContent = documentTitle;

    });

</script>

</body>
</html>
