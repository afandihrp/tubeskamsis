<?php
require 'db.php';
session_start();
use Dom\Mysql;



function checkDuplicate($con,$user)
{
    $sql = "SELECT username FROM accounts WHERE username = ?";
    $statement = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($statement, "s", $user);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    if (mysqli_stmt_num_rows($statement) > 0) {
        return true; // Username sudah digunakan
    } else {
        return false; // Username tersedia
    }
}



if (isset($_POST['username'], $_POST['password'])) {
    // $user = $_POST['username']; // TODO C1-4: Validasi input & gunakan prepared statement

    $options = [
        'options' => [
            'regexp' => '/^[a-zA-Z0-9_]{5,20}$/'
        ]
    ];

    $user = filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, $options);

    //$pass = $_POST['password']; // TODO C1-5: Simpan password dengan password_hash() bukan plaintext

    $pass = filter_input(INPUT_POST,'password', FILTER_VALIDATE_REGEXP, $options);

    if($user === false || $pass === false)
    {
        header('Location: register.php?error=1');
        exit;
    }

    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

    if(checkDuplicate($con,$user))
    {
        header('Location: register.php?error=2'); // Username sudah ada
        exit; 
    }
    else
    {
        $sql = "INSERT INTO accounts (username, password) VALUES ( ? , ? )"; // Rentan SQLi

        $statement = mysqli_prepare($con, $sql);

        mysqli_stmt_bind_param($statement, "ss", $user, $hashedPassword);

        if(mysqli_stmt_execute($statement))
        {
            header('Location: index.php');
            exit; 
        }
        else
        {       
            error_log("MySQL Error: " . mysqli_stmt_error($statement));
            header('Location: register.php?error=99'); // Error umum
            exit;        
        }
    }

    


    

    // $result = mysqli_stmt_get_result($statement);

 

    //mysqli_query($con, $sql) or die(mysqli_error($con)); // TODO C4-2: Jangan tampilkan error MySQL ke user
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <style>
        body {
            /* margin: 0px; */
        };
    </style>
<h2>Register</h2>
<form method="POST">
    <label>Username:</label><input type="text" name="username"><br>
    <label>Password:</label><input type="password" name="password"><br>
    <?php
        // Pertama, pastikan 'error' ada di URL
        if (isset($_GET['error'])) 
        {
            
            // Setelah dipastikan ada, baru kita cek nilainya
            // Menggunakan == lebih fleksibel karena data dari GET adalah string
            if ($_GET['error'] == 1) {
                echo '<p style="color:red; margin: 0px;">Please insert a valid username and password at least 5 characters</p>';
                
            } elseif ($_GET['error'] == 2) {
                echo '<p style="color:red; margin: 0px;">Username already exists.</p>';
            }
            // Anda bisa menambahkan elseif untuk error code lainnya di sini
            // elseif ($_GET['error'] == 3) { ... }
        }
    ?>
    <button type="submit">Register</button>    
</form>
<a href="index.php">Login</a>
</body>
</html>
