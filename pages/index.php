<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SkillConnect - Share & Learn Skills</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>SkillConnect</h1>
            <nav>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container" >
            <h2>Connect. Share. Grow.</h2>
            <p>Discover a community where skills are shared, taught, and learned. Whether you're offering your expertise or looking to learn something new—SkillConnect is the place to be.</p>
            <a href="register.php" class="btn-primary">Get Started</a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2>Why SkillConnect?</h2>
            <div class="feature-boxes">
                <div class="feature">
                    <h3>Post Skills</h3>
                    <p>Showcase what you're good at and help others grow with your knowledge.</p>
                </div>
                <div class="feature">
                    <h3>Request Help</h3>
                    <p>Looking to learn something new? Post a request and find someone to guide you.</p>
                </div>
                <div class="feature">
                    <h3>Connect with People</h3>
                    <p>Build meaningful connections based on learning and collaboration.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> SkillConnect. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
