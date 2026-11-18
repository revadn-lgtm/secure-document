
<?php

// Konfigurasi keamanan session
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');

    // Mengaktifkan secure cookie jika menggunakan HTTPS
    if (
        !empty($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off'
    ) {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

// Memeriksa status login admin
function isLoggedIn()
{
    return isset($_SESSION['admin_id']) &&
           filter_var(
               $_SESSION['admin_id'],
               FILTER_VALIDATE_INT
           ) !== false &&
           $_SESSION['admin_id'] > 0;
}

// Membatasi akses halaman admin
function requireLogin()
{
    if (!isLoggedIn()) {

        // Menghapus data session yang tidak valid
        $_SESSION = [];

        session_destroy();

        // Mengarahkan ke halaman login
        header('Location: ../login.php');
        exit;
    }
}

// Mengakhiri sesi admin
function requireLogout()
{
    if (session_status() === PHP_SESSION_ACTIVE) {

        // Menghapus seluruh data session
        $_SESSION = [];

        // Menghapus cookie session
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Menghancurkan session
        session_destroy();
    }

    header('Location: ../login.php');
    exit;
}

?>
