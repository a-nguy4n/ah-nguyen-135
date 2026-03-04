<?php
  if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Example login check 
    if($username === "admin" && $password === "1234") {
        echo "Login successful!";
    }
    else{
        echo "Invalid username or password.";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="/hw4/login/login.css">
</head>

<body>
  <form id="loginForm" class="login-container" method="POST">
    <h2>Login</h2>

    <fieldset class="input-group">
      <label class="input-group" for="username">Username</label>
      <input type="text"  
             name="username" 
             id="username" 
             placeholder="Enter your username" required>
    </fieldset>

    <fieldset class="input-group">
      <label for="password">Password</label>
      <input type="password" 
             name="password" 
             id="password" 
             placeholder="Enter your password" required>
    </fieldset>

    <button type="submit">Login</button>
  </form>
</body>
</html>