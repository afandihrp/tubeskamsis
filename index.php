<?php
// index.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body{
      
    }
   
  </style>
</head>
<body>
<h2>Login</h2>
<form method="POST" action="authenticate.php"><!-- TODO C1-1: Ganti GET menjadi POST dan tambahkan hidden token CSRF -->
  <?php
    if(isset($_SESSION['timeoutUntil']))
    {
      
      echo 
      '
        <style>
          label {
            color: red;
          }

          // input[type="text"],
          // input[type="password"] {
          //   border-color: red;
          // }
        </style>
      ';
    }

    if(empty($_SESSION['csrf_token']))
    {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      $token = $_SESSION['csrf_token'];
    }
  ?>
  <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
  <label>Username:</label><input type="text" name="username"><br>
  <label>Password:</label><input type="password" name="password"><br>
  <?php
    if(isset($_SESSION['timeoutUntil']))
    {
      echo '<h3 style="padding: 0px; margin: 0px; color:red;">TIMEOUT</h3>';
    }
    

  ?>
  <button type="submit">Login</button>
</form>
<?php
  if(isset($_GET['error']))
  {
    if($_GET['error'] == 1)
    {
      echo '<p style="padding: 0px; margin: 0px; color:red;">Password is incorrect</p>';
    }
    elseif($_GET['error'] == 2)
    {
      echo '<p style="padding: 0px; margin: 0px; color:red;">Username not found</p>';
    }
  }


?>
<!-- TODO C0-1: Arahkan semua traffic ke HTTPS dan pertimbangkan header HSTS -->
<p>Belum punya akun? <a href="register.php">Register di sini</a></p>
<?php
  foreach($_SERVER as $key => $value)
  {
    echo "{$key} = {$value} <br>";
  }


?>
</body>
</html>
