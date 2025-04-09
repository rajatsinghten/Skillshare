<?php
require_once('../includes/auth.php');
require_once('../includes/header.php');
?>

<h2>Welcome to Your Skill Dashboard</h2>

<p>Hello <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> 👋,</p>

<p>
    This is your personal space where you can manage your skill-sharing journey. Whether you're looking to offer your expertise or learn something new from others, you're in the right place!
</p>

<hr>

<h3>What would you like to do today?</h3>

<ul style="line-height: 2;">
    <li>
        ✅ <a href="post_skill.php">Post a New Skill</a><br>
        <small>Share what you're good at and help others grow.</small>
    </li>

    <li>
        🔍 <a href="search.php">Explore Skills from Others</a><br>
        <small>Find skills you want to learn and connect with talented users.</small>
    </li>

    <li>
        📨 <a href="inbox.php">Check Your Inbox</a><br>
        <small>View messages from users who want to connect with you.</small>
    </li>
</ul>

<hr>

<p style="color: gray;">Keep learning. Keep growing. 🌱</p>

<?php require_once('../includes/footer.php'); ?>
