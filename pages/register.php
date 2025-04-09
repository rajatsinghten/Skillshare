<?php require_once('../includes/header.php'); ?>

<h2>Register</h2>
<form method="POST" action="../includes/auth.php">
    <label>Full Name:</label>
    <input type="text" name="name" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="register" value="Register">
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

<?php require_once('../includes/footer.php'); ?>