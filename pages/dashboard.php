<?php
require_once('../includes/auth.php');
require_once('../includes/header.php');

// Sample featured skills (can later be fetched from DB)
$featuredSkills = [
    [
        'image' => '../uploads/web_dev.jpg',
        'title' => 'Web Development',
        'description' => 'Build websites using HTML, CSS, JS.',
    ],
    [
        'image' => '../uploads/guitar.jpg',
        'title' => 'Guitar Basics',
        'description' => 'Learn chords and strumming techniques.',
    ],
    [
        'image' => '../uploads/photography.jpg',
        'title' => 'Photography',
        'description' => 'Capture stunning moments like a pro.',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SkillShare</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome to Your Skill Dashboard</h2>
    <p>Hello <strong><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?></strong> 👋,</p>
    <p>This is your space to share and learn skills with others!</p>

    <!-- 🌟 Featured Skills -->
    <h3 class="center-heading">✨ Featured Skills</h3>
    <div class="featured-skills">
        <div class="skill-card">
            <img src="../uploads/web_dev.jpg" alt="Skill Image">
            <h4>Web Development</h4>
            <p>Build websites using HTML, CSS, JS.</p>
        </div>
        
        <div class="skill-card">
            <img src="../uploads/guitar.webp" alt="Skill Image">
            <h4>Guitar Basics</h4>
            <p>Learn chords and strumming techniques.</p>
        </div>

        <div class="skill-card">
            <img src="../uploads/photo.jpg" alt="Skill Image">
            <h4>Photography</h4>
            <p>Capture stunning moments like a pro.</p>
        </div>
    </div>


    <div class="view-more-wrapper">
        <a href="search.php" class="view-more-btn">🔍 View More Skills</a>
    </div>

    <!-- 🧭 Dashboard Actions -->
    <div class="dashboard-actions">
        <h3 class="center-heading">What would you like to do today?</h3>
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
</body>
</html>