<?php
require_once('../includes/auth.php');
require_once('../includes/header.php');
?>

<div class="dashboard-container">
    <h2>Welcome to Your Skill Dashboard</h2>

    <p>Hello <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> 👋,</p>

    <p>
        This is your personal space where you can manage your skill-sharing journey. Whether you're looking to offer your expertise or learn something new from others, you're in the right place!
    </p>

    <div class="dashboard-actions">
        <h3>What would you like to do today?</h3>
        <ul>
            <li>
                ✅ <a href="post_skill.php">Post a New Skill</a>
                <span class="description">Share what you're good at and help others grow.</span>
            </li>
            <li>
                🔍 <a href="search.php">Explore Skills from Others</a>
                <span class="description">Find skills you want to learn and connect with talented users.</span>
            </li>
            <li>
                📨 <a href="inbox.php">Check Your Inbox</a>
                <span class="description">View messages from users who want to connect with you.</span>
            </li>
        </ul>
    </div>

    <p class="footer-message">Keep learning. Keep growing. 🌱</p>
</div>

<?php require_once('../includes/footer.php'); ?>
