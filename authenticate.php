<?php

use Dom\Mysql;

session_start();
require 'db.php';

const maxAttempt = 3;
const timeoutTime = 5; 

// function verifyPassword ($con, $user, $pass)
// {
//     $sql = "SELECT id, password FROM accounts WHERE username = ?";
//     $statement = mysqli_prepare($con, $sql);
//     mysqli_stmt_bind_param($statement, "s", $user);
//     mysqli_stmt_execute($statement);
//     mysqli_stmt_bind_result($statement, $id, $hashedPasswordFromDB);
//     if(mysqli_stmt_fetch($statement))
//     {
//         if(password_verify($pass, $hashedPasswordFromDB))
//         {
//             return true;
//             // die("true");        
//         }
//         else
//         {
//             // die("false"); 
//             return false;
//         }
//     }
    
// }

if(isset($_SESSION['timeoutUntil']) && $_SESSION['timeoutUntil']<=time())
{
    unset($_SESSION['timeoutUntil']);
    unset($_SESSION['attemptedLogin']);
    echo "done";
    header("Location: index.php");
    exit;        
}

if(isset($_SESSION['timeoutUntil']))
{
    header("Location: index.php");
    exit;
}

// TODO C0-2: Implementasi brute-force limit

if (!isset($_SESSION['attemptedLogin'])) 
{
    $_SESSION['attemptedLogin'] = 1;
    echo "<br> true";
} 
else 
{
    $_SESSION['attemptedLogin']++;
    if(!isset($_SESSION['timeoutUntil']) && $_SESSION['attemptedLogin']>=maxAttempt)
    {
        $_SESSION['timeoutUntil']=time()+timeoutTime;
        echo "bisaa";
    }
    echo "{$_SESSION['timeoutUntil']}";
    
}




// Pastikan request datang dari metode POST
if($_SERVER['REQUEST_METHOD'] != 'POST') 
{
    die('Metode tidak diizinkan.');
}


// Verifikasi token CSRF
// hash_equals() digunakan untuk perbandingan yang aman dari timing attack
if (!isset($_POST['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Token CSRF tidak valid. Permintaan ditolak.');
}

// Setelah verifikasi, token bisa dihapus agar tidak bisa digunakan lagi
unset($_SESSION['csrf_token']);







// TODO C1-2: Gunakan POST + token CSRF, bukan GET
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

// TODO C1-3: Ganti query concat string ini dengan prepared statement
//$sql = "SELECT id, password FROM accounts WHERE username = '$user' AND password = '$pass'";

// $sql = "SELECT id, password FROM accounts WHERE username = ? AND password = ?";

$sql = "SELECT id, password FROM accounts WHERE username = ?";
$statement = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($statement, "s", $user);
mysqli_stmt_execute($statement);
mysqli_stmt_bind_result($statement, $id, $hashedPasswordFromDB);
// mysqli_stmt_fetch($statement);

// die("{$id} dan {$hashedPasswordFromDB}");

//$res = mysqli_query($con, $sql) or die(mysqli_error($con)); // TODO C4-3: Simpan error ke log, bukan ke output

// TODO C1-6: Verifikasi password pakai password_verify(), bukan plaintext
if(mysqli_stmt_fetch($statement))
{
    if(password_verify($pass, $hashedPasswordFromDB))
    {
        session_regenerate_id(); // TODO C2-1: Panggil session_regenerate_id() untuk mencegah session fixation
        $_SESSION['account_loggedin'] = true;
        $_SESSION['account_id'] = $id;
        $_SESSION['account_name'] = $user;
        // die("{$id} <br> {$user}");

        header('Location: home.php');
        unset($_SESSION['timeoutUntil']);
        unset($_SESSION['attemptedLogin']);
        exit;

    }
    else
    {
        header("Location: index.php?error=1");
        exit;
    }
}
else
{
    header("Location: index.php?error=2");
    exit;
}



// header("Location: {$_SERVER['PHP_SELF']}");
// header("Refresh: 0");
header("Location: index.php");
// exit;

?>
