<?php
require_once('../includes/auth.php');
require_once('../includes/header.php');
?>

<h2>Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['user_name']; ?>! Manage your skills or explore others below.</p>
<a href="post_skill.php">Post a New Skill</a>

<?php require_once('../includes/footer.php'); ?>