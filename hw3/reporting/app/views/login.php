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
  <link rel="stylesheet" href="hw3/reporting/app/views/login.css"> 

  <style> 
    @font-face{
        font-family: "Space Mono";
        src: url("/fonts/Space_Mono/SpaceMono-Regular.ttf");
    }

    *{
      box-sizing: border-box;
      font-family: "Space Mono", sans-serif;
    }

    body{
      background: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      
      background: white;
      background-image:
        radial-gradient(
          circle at top right,
          rgba(15, 105, 154, 0.813) 0%,
          rgb(15, 106, 154) 15%,
          rgb(15, 106, 154) 25%,
          white 60%
        );
    }

    .login-container{
      background: white;
      padding: 40px;
      border-radius: 10px;
      width: 320px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    h2{
      margin-bottom: 20px;
    }

    .input-group{
      text-align: left;
      margin-bottom: 15px;
      border: none;
    }

    .input-group label{
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .input-group input{
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button{
      width: 90%;
      padding: 10px;
      border: none;
      background: #0f5a76;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover{
      background: #45a049;
    }
  </style>
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