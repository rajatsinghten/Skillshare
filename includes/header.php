<!DOCTYPE html>
<html>
<head>
    <title>SkillShare - Connect & Learn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #333;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header styles */
        .site-header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 50px; /* Fixed height for consistency */
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ecf0f1;
            text-decoration: none;
        }
        
        .logo span {
            color: #3498db;
        }
        
        /* Navigation styles */
        .main-nav {
            display: flex;
            gap: 20px;
            height: 100%;
            align-items: center;
        }
        
        .main-nav a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            height: 36px;
        }
        
        .main-nav a:hover {
            background-color: #3498db;
            color: white;
        }
        
        .main-nav a.active {
            background-color: #3498db; 
            color: white;
        }
        
        /* Main content container */
        .main-content {
            flex: 1 1 auto;
            padding: 0;
        }
        
        /* Footer styles */
        .site-footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 15px 0;
            text-align: center;
            margin-top: 30px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                height: auto;
                padding: 10px 0;
            }
            
            .main-nav {
                margin-top: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .main-nav a {
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-content">
            <div class="logo">Skill<span>Share</span></div>
            <nav class="main-nav">
                <a href="../pages/dashboard.php">Dashboard</a>
                <a href="../pages/post_skill.php">Post Skill</a>
                <a href="../pages/my_skills.php">My Skills</a>
                <a href="../pages/inbox.php">Inbox</a>
                <a href="../pages/chat.php">Chat</a>
                <a href="../pages/search.php">Search</a>
                <a href="../includes/logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <div class="main-content">
        <div class="container">
            <!-- Page content will be placed here -->
        </div>
    </div>
    
</body>
</html>

