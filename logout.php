<?php
session_start();

// C2-3: Hapus semua variabel sesi
$_SESSION = [];

// Jika ingin benar-benar hapus cookie sesi dari browser:
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi
session_destroy();

// C2-4: Regenerasi ID sesi untuk mencegah reuse ID lama
session_start(); // mulai ulang sesi kosong
session_regenerate_id(true); // buat ID sesi baru & hapus lama

// Redirect ke halaman utama
header('Location: index.php');
exit;
?>
