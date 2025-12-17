<?php
$serverName="localhost\\SQLEXPRESS"; 
$connectionOptions=[ 
"Database"=>"travelwebsite", 
"Uid"=>"", 
"PWD"=>"" 
]; 
$conn=sqlsrv_connect($serverName, $connectionOptions); 

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (!$email || !$password) {
        $error_message = 'All fields are required.';
    } else {
        $sqllog="SELECT EMAIL, PASSWORD FROM USERS WHERE EMAIL = ?";
        $paramslog=[$email];
        $stmtlog=sqlsrv_query($conn, $sqllog, $paramslog);
        if($stmtlog === false){
            die(print_r(sqlsrv_errors(),true));
        }

        $row = sqlsrv_fetch_array($stmtlog, SQLSRV_FETCH_ASSOC);
        if($row){
            if($row['EMAIL'] == $email && $row['PASSWORD'] == $password){
                header("Location: logdex.php");
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
    }
}


?>







<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login to Wanderer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: #181818;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #2b2b2b;
    }

    .login-container {
      background: #2b2b2bff;
      border-radius: 1.5rem;
      box-shadow: 0 0px 60px rgba(255, 255, 0, 0.3);
      overflow: hidden;
      max-width: 900px;
      width: 100%;
    }

    .login-wrapper {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
    }

    .login-form-section {
      padding: 3rem 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-image-section {
      background: url("https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg") center/cover no-repeat;
      position: relative;
      min-height: 450px;
    }

    .login-image-section::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.65), rgba(0,0,0,0.5));
    }

    .login-image-text {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      color: #d8ca25ff;
      z-index: 2;
    }

    .login-image-text h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .login-image-text p {
      font-size: 1rem;
      line-height: 1.6;
      max-width: 300px;
    }

    .brand-logo {
      font-weight: 700;
      letter-spacing: 0.18em;
      font-size: 0.9rem;
      text-transform: uppercase;
      color: #cdb73dea;
      margin-bottom: 1.5rem;
    }

    .form-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #c1c0c0ff;
    }

    .form-subtitle {
      font-size: 0.9rem;
      color: #999;
      margin-bottom: 2rem;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    .form-group label {
      font-weight: 600;
      color: #2b2b2b;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .form-control {
      border: 1px solid #e0e0e0;
      border-radius: 0.6rem;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: #c28a3b;
      box-shadow: 0 0 0 3px rgba(194, 138, 59, 0.1);
      background-color: #3b3b3bff;
    }

    .btn-login {
      background-color: #cdb73dea;
      border-color: #cdb73dea;
      color: #2f2f2fff;
      border-radius: 0.6rem;
      padding: 0.75rem 1rem;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s;
    }

    .btn-login:hover {
      background-color: #a9742f;
      border-color: #a9742f;
      color: #fff;
    }

    .form-footer {
      margin-top: 1.5rem;
      text-align: center;
      font-size: 0.9rem;
      color: #666;
    }

    .form-footer a {
      color: #c28a3b;
      text-decoration: none;
      font-weight: 600;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }

    .checkbox-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }

    .checkbox-group a {
      color: #ead266ff;
      text-decoration: none;
    }

    @media (max-width: 768px) {
      .login-wrapper {
        grid-template-columns: 1fr;
      }

      .login-image-section {
        display: none;
      }

      .login-form-section {
        padding: 2rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-wrapper">
      <!-- Form Section -->
      <div class="login-form-section">
        <div class="brand-logo">Wanderer</div>
        
        <h1 class="form-title">Welcome Back!</h1>
        <p class="form-subtitle">Sign in to your account to continue exploring</p>

        <?php if ($error_message): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form id="login-form" method="POST" action="login.php">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required />
          </div>

          <button type="submit" class="btn btn-login">Sign In</button>
          
        </form>

        <div class="form-footer">
        Don't have an account? <a href="signup.php">Sign up here</a><br>

        <a href="index.html" class="btn btn-outline-secondary btn-sm mb-3">← Back to home </a>
        </div>
      </div>

      <!-- Image Section -->
      <div class="login-image-section">
        <div class="login-image-text">
          <h2>Explore the World</h2>
          <p>Sign in to start your adventure with Wanderer.</p>
        </div>
      </div>
    </div>
  </div>


</body>
</html>