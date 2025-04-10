<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
require_once('../includes/header.php');

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch accepted connections
$connections_sql = "
    SELECT 
        u.id,
        u.name,
        m.timestamp AS connected_since
    FROM 
        messages m
    JOIN 
        users u ON (m.from_id = u.id OR m.to_id = u.id)
    WHERE 
        ((m.from_id = ? AND m.to_id = u.id) OR (m.to_id = ? AND m.from_id = u.id))
        AND m.status = 'accepted'
    GROUP BY
        u.id
    ORDER BY 
        connected_since DESC
";

$connections_stmt = mysqli_prepare($conn, $connections_sql);
mysqli_stmt_bind_param($connections_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($connections_stmt);
$connections_result = mysqli_stmt_get_result($connections_stmt);

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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        /* Dashboard layout */
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h2, h3 {
            color: #2c3e50;
        }
        
        .center-heading {
            text-align: center;
            margin: 30px 0 20px;
            color: #3498db;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        
        /* Featured Skills */
        .featured-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .skill-card {
            flex: 0 0 calc(33.333% - 20px);
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .skill-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .skill-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .skill-card h4 {
            margin: 15px 15px 10px;
            color: #2c3e50;
        }
        
        .skill-card p {
            margin: 0 15px 15px;
            color: #7f8c8d;
        }
        
        /* Connections Section */
        .connections-section {
            margin: 30px 0;
        }
        
        .connections-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        
        .connection-card {
            flex: 0 0 calc(25% - 15px);
            min-width: 200px;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            border: 1px solid #e1e8ed;
            transition: transform 0.2s ease;
        }
        
        .connection-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-color: #3498db;
        }
        
        .connection-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 10px;
            background-color: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
        }
        
        .connection-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .connection-date {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .connection-actions {
            margin-top: 10px;
        }
        
        .connection-actions a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            transition: background-color 0.3s;
        }
        
        .connection-actions a:hover {
            background-color: #2980b9;
        }
        
        /* Dashboard Actions */
        .dashboard-actions {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        
        .dashboard-actions ul {
            list-style-type: none;
            padding: 0;
        }
        
        .dashboard-actions li {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .dashboard-actions li:hover {
            transform: translateX(5px);
            background-color: #f0f7ff;
        }
        
        .dashboard-actions a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        
        .dashboard-actions .description {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .view-more-wrapper {
            text-align: center;
            margin: 20px 0;
        }
        
        .view-more-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .view-more-btn:hover {
            background-color: #2980b9;
        }
        
        .footer-message {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .skill-card {
                flex: 0 0 calc(50% - 20px);
            }
            
            .connection-card {
                flex: 0 0 calc(50% - 15px);
            }
        }
        
        @media (max-width: 480px) {
            .skill-card, .connection-card {
                flex: 0 0 100%;
            }
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome to Your Skill Dashboard</h2>
    <p>Hello <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> 👋,</p>
    <p>This is your space to share and learn skills with others!</p>
    
    <!-- 🤝 Your Connections -->
    <h3 class="center-heading">🤝 Your Connections</h3>
    <div class="connections-section">
        <?php if (mysqli_num_rows($connections_result) > 0): ?>
            <div class="connections-grid">
                <?php while ($connection = mysqli_fetch_assoc($connections_result)): ?>
                    <div class="connection-card">
                        <div class="connection-avatar">
                            <?php echo strtoupper(substr($connection['name'], 0, 1)); ?>
                        </div>
                        <div class="connection-name"><?php echo htmlspecialchars($connection['name']); ?></div>
                        <div class="connection-date">Connected since: <?php echo date('M d, Y', strtotime($connection['connected_since'])); ?></div>
                        <div class="connection-actions">
                            <a href="messages.php?user=<?php echo $connection['id']; ?>">Message</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">You don't have any connections yet. Go to the <a href="search.php">Search page</a> to find users to connect with!</p>
        <?php endif; ?>
    </div>

    <!-- 🌟 Featured Skills -->
    <h3 class="center-heading">✨ Featured Skills</h3>
    <div class="featured-skills">
        <?php foreach ($featuredSkills as $skill): ?>
            <div class="skill-card">
                <img src="<?php echo $skill['image']; ?>" alt="Skill Image">
                <h4><?php echo $skill['title']; ?></h4>
                <p><?php echo $skill['description']; ?></p>
            </div>
        <?php endforeach; ?>
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