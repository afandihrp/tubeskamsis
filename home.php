<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
require 'db.php';

// TODO C2-2: Set flag cookie Secure, HttpOnly, SameSite menggunakan ini_set atau header
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Home</title></head>
<body>
<h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['account_name']); // TODO C3-1: Escape output dengan htmlspecialchars() ?></h2>
<!-- <p><a href="profile.php?id=<?php echo $_SESSION['account_id']; ?>">Lihat Profil</a></p> -->
 
<form action="profile.php" method="post" >
    <input type="hidden" name="id" value="<?php echo $_SESSION['account_id']; ?>">
    <input type="submit" value="Lihat Profile">
</form>

<p><a href="logout.php">Logout</a></p>
</body>
</html>
