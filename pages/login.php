<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SkillConnect</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Welcome Back!</h2>
        <p>Please login to your SkillShare account</p>

        <form method="POST" action="../includes/auth.php">
            <label for="email">Email</label>
            <input type="email" name="email" required placeholder="Enter your email">

            <label for="password">Password</label>
            <input type="password" name="password" required placeholder="Enter your password">

            <button type="submit" name="login">Login</button>
        </form>

        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
