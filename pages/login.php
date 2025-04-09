<?php
session_start();
require_once('../includes/db.php'); // Your DB connection file

$error = '';
$loginFailed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if ($row = mysqli_fetch_assoc($query)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: dashboard.php"); // ✅ Redirect to dashboard on success
            exit;
        } else {
            $error = "❌ Invalid email or password.";
            $loginFailed = true;
        }
    } else {
        $error = "❌ Invalid email or password.";
        $loginFailed = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SkillConnect</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Welcome Back!</h2>
        <p>Please login to your SkillShare account</p>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input 
                type="email" 
                name="email" 
                required 
                placeholder="Enter your email" 
                class="<?php echo ($loginFailed ? 'input-error' : ''); ?>"
            >

            <label for="password">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required 
                placeholder="Enter your password" 
                class="<?php echo ($loginFailed ? 'input-error' : ''); ?>"
            >

            <!-- Show Password toggle -->
            <div style="margin-top: 5px;">
                <input type="checkbox" id="togglePassword">
                <label for="togglePassword" style="font-size: 14px;">Show Password</label>
            </div>

            <!-- Error message -->
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>

            <button type="submit" name="login">Login</button>
        </form>


        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('change', function () {
        password.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>
