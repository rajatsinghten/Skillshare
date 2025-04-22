<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
require_once('../includes/header.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify session and user_id
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user name from database if not in session
if (!isset($_SESSION['user_name'])) {
    $user_sql = "SELECT name FROM users WHERE id = ?";
    $user_stmt = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    
    if ($user = mysqli_fetch_assoc($user_result)) {
        $_SESSION['user_name'] = $user['name'];
    } else {
        $_SESSION['user_name'] = 'User'; // Default if not found
    }
}
// Fetch skills shared by the user count
$skills_shared_count = 0;
// *** ASSUMPTION: You have a 'skills' table with a 'user_id' column ***
$skills_sql = "SELECT COUNT(*) as count FROM skills WHERE user_id = ?";
$skills_stmt = mysqli_prepare($conn, $skills_sql);
if ($skills_stmt) {
    mysqli_stmt_bind_param($skills_stmt, "i", $user_id);
    mysqli_stmt_execute($skills_stmt);
    $skills_result = mysqli_stmt_get_result($skills_stmt);
    if ($row = mysqli_fetch_assoc($skills_result)) {
        $skills_shared_count = $row['count'];
    }
    mysqli_stmt_close($skills_stmt);
} else {
    error_log("Failed to prepare skills count query: " . mysqli_error($conn));
}


$new_messages_count = 0;
$messages_sql = "SELECT COUNT(*) as count FROM messages WHERE to_id = ? AND status = 'unread'"; // Or maybe 'pending' depending on your system
$messages_stmt = mysqli_prepare($conn, $messages_sql);
if ($messages_stmt) {
    mysqli_stmt_bind_param($messages_stmt, "i", $user_id);
    mysqli_stmt_execute($messages_stmt);
    $messages_result = mysqli_stmt_get_result($messages_stmt);
    if ($row = mysqli_fetch_assoc($messages_result)) {
        $new_messages_count = $row['count'];
    }
    mysqli_stmt_close($messages_stmt);
} else {
     error_log("Failed to prepare new messages count query: " . mysqli_error($conn));
}


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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SkillShare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="../assets/css/dashboard.css"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
    --primary-color:rgb(219, 43, 128);
    --secondary-color:rgb(195, 39, 45);
    --light-bg: #f8f9fa;
    --text-dark: #2b2d42;
}

.dashboard-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.welcome-banner {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 1rem;
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quick-actions {
    display: flex;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.action-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.dashboard-section {
    margin: 3rem 0;
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.connections-grid, .skills-grid, .resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.connection-card, .skill-card, .resource-card {
    background: var(--light-bg);
    padding: 1.5rem;
    border-radius: 0.5rem;
    transition: transform 0.2s;
}

.connection-card:hover {
    transform: translateY(-5px);
}

.activity-list {
    margin: 1rem 0;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-bg);
    margin: 0.5rem 0;
    border-radius: 0.5rem;
}
.dashboard-section {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px; /* Add space below the section */
        }

        .dashboard-section h2 {
            font-size: 1.8em; /* Larger heading */
            color: #2c3e50; /* Dark blue heading color */
            margin-bottom: 25px; /* More space below heading */
            border-bottom: 2px solid #e0e0e0; /* Subtle separator */
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .dashboard-section h2 i {
            margin-right: 12px; /* Space between icon and text */
            color: #f39c12; /* Gold star color */
        }

        /* Skills Grid Layout */
        .skills-grid {
            display: grid;
            /* Responsive grid: columns adjust automatically */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px; /* Space between cards */
        }

        /* Skill Card Styling */
        .skill-card {
            background-color: #fff;
            border-radius: 10px; /* Slightly more rounded corners */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden; /* Ensures image corners are clipped */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
            display: flex;
            flex-direction: column; /* Stack image and content vertically */
        }

        .skill-card:hover {
            transform: translateY(-5px); /* Lift card on hover */
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12); /* Enhance shadow on hover */
        }

        .skill-card img {
            display: block;
            width: 100%;
            height: 180px; /* Fixed height for images */
            object-fit: cover; /* Crop image to fit */
            border-bottom: 1px solid #eee; /* Separator */
        }

        .skill-content {
            padding: 20px;
            flex-grow: 1; /* Allow content to fill remaining space */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes meta to bottom */
        }

        .skill-content h4 {
            font-size: 1.25em;
            color: #34495e; /* Slightly darker blue */
            margin-top: 0;
            margin-bottom: 10px;
        }

        .skill-content p {
            font-size: 0.95em;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
            flex-grow: 1; /* Allow paragraph to take available space */
        }

        /* Skill Meta (Learners & Button) */
        .skill-meta {
            display: flex;
            justify-content: space-between; /* Space out learners and button */
            align-items: center;
            margin-top: auto; /* Push to the bottom */
            padding-top: 10px; /* Add some space above */
            border-top: 1px solid #f0f0f0; /* Subtle separator */
        }

        .skill-meta span {
            font-size: 0.9em;
            color: #7f8c8d; /* Grey color for meta text */
            display: flex;
            align-items: center;
        }

        .skill-meta span i {
            margin-right: 6px; /* Space icon and text */
            color: #3498db; /* Blue user icon */
        }

        /* Interest Button Styling */
        .interest-btn {
            background-color: #3498db; /* Primary blue */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .interest-btn:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }


@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        flex-direction: column;
    }
}
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Welcome Header -->
    <div class="welcome-banner">
        <h1>Welcome Back, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?>! <span class="wave">👋</span></h1>
        <p class="subtitle">Let's continue your learning journey</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-handshake"></i>
            <h3><?php echo mysqli_num_rows($connections_result); ?></h3>
            <p>Active Connections</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-share-alt"></i>
            <h3><?php echo $skills_shared_count; ?></h3>
            <p>Skills Shared</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-inbox"></i>
            <h3><?php echo $new_messages_count; ?></h3>
            <p>New Messages</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <button class="action-btn" onclick="location.href='post_skill.php'">
            <i class="fas fa-plus-circle"></i> Post Skill
        </button>
        <button class="action-btn" onclick="location.href='search.php'">
            <i class="fas fa-search"></i> Find Learners
        </button>
        <button class="action-btn" onclick="location.href='inbox.php'">
            <i class="fas fa-inbox"></i> Check Inbox
        </button>
    </div>

    <!-- Connections Section -->
    <section class="dashboard-section">
        <h2><i class="fas fa-users"></i> Your Connections</h2>
        <div class="connections-grid">
            <?php if (mysqli_num_rows($connections_result) > 0): ?>
                <?php while ($connection = mysqli_fetch_assoc($connections_result)): ?>
                    <div class="connection-card">
                        <div class="avatar"><?php echo strtoupper(substr($connection['name'], 0, 1)); ?></div>
                        <h4><?php echo htmlspecialchars($connection['name']); ?></h4>
                        <p class="connection-date">Connected: <?php echo date('M Y', strtotime($connection['connected_since'])); ?></p>
                        <button class="chat-btn" onclick="location.href='chat.php?user=<?php echo $connection['id']; ?>'">
                            <i class="fas fa-comment-dots"></i> Chat
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No connections yet! Start by exploring skills:</p>
                    <button onclick="location.href='search.php'">Browse Skills</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-section">
    <h2><i class="fas fa-star"></i> Recommended Skills</h2>
    <div id="featured-skills" class="skills-grid">
        </div>
</section>
<section class="dashboard-section">
    <h2><i class="fas fa-history"></i> Recent Activity</h2>
    <div id="recent-activity" class="activity-list">
        </div>
    <form id="add-activity" class="activity-form">
        <input type="text" placeholder="Add custom note..." required>
        <button type="submit"><i class="fas fa-plus"></i> Add Note</button>
    </form>
</section>

<section class="dashboard-section">
    <h2><i class="fas fa-lightbulb"></i> Learning Resources</h2>
    <div class="resources-grid">
        <div class="resource-card">
            <h4><i class="fas fa-video"></i> Tutorial Videos</h4>
            <p>Watch our beginner-friendly tutorials</p>
            <button class="resource-btn">View Library</button>
        </div>
        <div class="resource-card">
            <h4><i class="fas fa-book"></i> Documentation</h4>
            <p>Explore our comprehensive guides</p>
            <button class="resource-btn">Read Docs</button>
        </div>
    </div>
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Featured Skills Logic with Local Storage
    const skillsContainer = document.getElementById('featured-skills');
    const defaultSkills = [
        {
            id: 'web-dev-1',
            img: "https://placehold.co/600x400/5DADE2/FFFFFF?text=Web+Dev",
            alt: "Web Development Concept Image",
            title: "Modern Web Development",
            description: "Master HTML5, CSS3, Flexbox, Grid, and modern JavaScript frameworks.",
            learners: 112,
            interested: false
        },
        {
            id: 'guitar-2',
            img: "https://placehold.co/600x400/F5B041/FFFFFF?text=Guitar",
            alt: "Acoustic Guitar",
            title: "Acoustic Guitar Basics",
            description: "Learn essential chords, strumming patterns, and your first few songs.",
            learners: 85,
            interested: false
        },
        {
            id: 'data-analysis-3',
            img: "https://placehold.co/600x400/AF7AC5/FFFFFF?text=Data+Analysis",
            alt: "Data Analysis Charts",
            title: "Introduction to Data Analysis",
            description: "Understand data concepts, basic statistics, and tools like Excel or Python Pandas.",
            learners: 98,
            interested: false
        },
        {
            id: 'graphic-design-4',
            img: "https://placehold.co/600x400/48C9B0/FFFFFF?text=Graphic+Design",
            alt: "Graphic Design Tools",
            title: "Graphic Design Fundamentals",
            description: "Explore principles of design, color theory, typography, and layout techniques.",
            learners: 76,
            interested: false
        },
        {
            id: 'cooking-5',
            img: "https://placehold.co/600x400/EC7063/FFFFFF?text=Cooking",
            alt: "Cooking Ingredients",
            title: "Basic Culinary Skills",
            description: "Learn essential knife skills, cooking methods, and how to follow recipes.",
            learners: 55,
            interested: false
        }
    ];

    let storedSkills = localStorage.getItem('featuredSkills');
    let skills = storedSkills ? JSON.parse(storedSkills) : defaultSkills;

    function renderSkills() {
        skillsContainer.innerHTML = skills.map(skill => `
            <div class="skill-card ${skill.interested ? 'interested' : ''}">
                <img src="${skill.img}" alt="${skill.alt}">
                <div class="skill-content">
                    <h4>${skill.title}</h4>
                    <p>${skill.description}</p>
                    <div class="skill-meta">
                        <span><i class="fas fa-users"></i> ${skill.learners + (skill.interested ? 1 : 0)} learners</span>
                        <button class="interest-btn ${skill.interested ? 'active' : ''}" data-skill-id="${skill.id}">
                            ${skill.interested ? 'Interested' : 'Express Interest'}
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Add event listeners to the "Express Interest" buttons
        const interestButtons = document.querySelectorAll('.interest-btn');
        interestButtons.forEach(button => {
            button.addEventListener('click', function() {
                const skillId = this.dataset.skillId;
                const skillIndex = skills.findIndex(skill => skill.id === skillId);
                if (skillIndex !== -1) {
                    skills[skillIndex].interested = !skills[skillIndex].interested;
                    localStorage.setItem('featuredSkills', JSON.stringify(skills));
                    renderSkills(); // Re-render to update the UI
                }
            });
        });
    }

    renderSkills();

    // LocalStorage for Recent Activity
    const activityList = document.getElementById('recent-activity');
    let activities = JSON.parse(localStorage.getItem('dashboardActivities') || '[]');

    function renderActivities() {
        activityList.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <i class="fas fa-sticky-note"></i>
                <div>
                    <p>${activity.text}</p>
                    <small>${new Date(activity.date).toLocaleString()}</small>
                </div>
            </div>
        `).join('');
    }

    renderActivities();

    // Add new activity
    document.getElementById('add-activity').addEventListener('submit', (e) => {
        e.preventDefault();
        const noteInput = e.target.querySelector('input');
        const text = noteInput.value.trim();
        if (text) {
            const newActivity = {
                text,
                date: new Date().toISOString()
            };
            activities.unshift(newActivity);
            localStorage.setItem('dashboardActivities', JSON.stringify(activities));
            renderActivities();
            noteInput.value = ''; // Clear the input field
        }
    });
});
</script>
<?php require_once('../includes/footer.php'); ?>
</body>
</html>