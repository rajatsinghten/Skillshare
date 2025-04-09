<?php require_once('../includes/header.php'); ?>

<h2>Login</h2>
<form method="POST" action="../includes/auth.php">
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="login" value="Login">
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php require_once('../includes/footer.php'); ?>