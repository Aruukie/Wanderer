<?php
$serverName="localhost\\SQLEXPRESS"; 
$connectionOptions=[ 
"Database"=>"travelwebsite", 
"Uid"=>"", 
"PWD"=>"" 
]; 
$conn=sqlsrv_connect($serverName, $connectionOptions); 
if($conn==false) 
die(print_r(sqlsrv_errors(),true)); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];

    if (!$email || !$password || !$first_name || !$last_name) {
        die('All fields are required.');
    }

    $stmt = "INSERT INTO USERS (FIRST_NAME, LAST_NAME, EMAIL, PASSWORD) VALUES (?, ?, ?, ?)";
    $params = [$first_name, $last_name, $email, $password];
    $result = sqlsrv_query($conn, $stmt, $params);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        header("Location: login.php");
        exit();
    }
}



?>






<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign up for Wanderer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 [web:44] -->
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
      padding: 5rem;
    }

    .signup-container {
      background: #2b2b2bff;
      border-radius: 1.5rem;
      box-shadow: 0 0px 60px rgba(255, 255, 0, 0.3);
      overflow: hidden;
      max-width: 900px;
      width: 100%;
    }

    .signup-wrapper {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
    }

    .signup-form-section {
      padding: 3rem 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .signup-image-section {
      background: url("https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg") center/cover no-repeat;
      position: relative;
      min-height: 450px;
    }

    .signup-image-section::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.65), rgba(0,0,0,0.5));
    }

    .signup-image-text {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      color: #fff;
      z-index: 2;
    }

    .signup-image-text h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 1rem;
      color: #e7dc00ff;
    }

    .signup-image-text p {
      font-size: 1rem;
      line-height: 1.6;
      max-width: 320px;
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
      background-color: #faf8f3;
    }

    .btn-signup {
      background-color: #cdb73dea;
      border-color: #cdb73dea;
      color: #fff;
      border-radius: 0.6rem;
      padding: 0.75rem 1rem;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s;
    }

    .btn-signup:hover {
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

    @media (max-width: 768px) {
      .signup-wrapper {
        grid-template-columns: 1fr;
      }

      .signup-image-section {
        display: none;
      }

      .signup-form-section {
        padding: 2rem;
      }
    }
  </style>
</head>
<body>

  <div class="signup-container">
    <div class="signup-wrapper">
      <!-- Form Section -->
      <div class="signup-form-section">
        <div class="brand-logo">Wanderer</div>
        <h1 class="form-title">Create your account</h1>
        <p class="form-subtitle">Join Wanderer and start planning your next adventure.</p>

        <!-- point action/method to your PHP handler -->
        <form id="signup-form" method="POST" action="signup.php">
          <div class="form-group">
            <label for="fname">First name</label>
            <input type="text" class="form-control" id="fname" name="fname" placeholder="John" required />
          </div>

          <div class="form-group">
            <label for="lname">Last name</label>
            <input type="text" class="form-control" id="lname" name="lname" placeholder="Dog" required />
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required />
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required />
          </div>

          <button type="submit" class="btn btn-signup">Create account</button>
        </form>

        <div class="form-footer">
          Already have an account?
          <a href="login.php">Sign in here</a>
        </div>
      </div>

      <!-- Image Section -->
      <div class="signup-image-section">
        <div class="signup-image-text">
          <h2>Start your journey</h2>
          <p>Create your Wanderer account and unlock hand‑picked stays and destinations tailored to you.</p>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
