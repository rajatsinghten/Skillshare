<?php
include("../includes/db.php");
include("../includes/auth.php");
include("../includes/header.php");

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM skills WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<style>
    /* Skills page specific styles */
    .skills-container {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    h2 {
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
        border-bottom: 2px solid #ecf0f1;
        padding-bottom: 10px;
    }
    
    .skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .skill-card {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .skill-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .skill-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    
    .skill-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .skill-card h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: #2c3e50;
    }
    
    .skill-card .desc {
        color: #7f8c8d;
        margin-bottom: 15px;
        line-height: 1.5;
    }
    
    .skill-info {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        font-size: 14px;
        margin-top: auto;
        padding-top: 15px;
    }
    
    .skill-type {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .type-offer {
        background-color: #3498db;
        color: white;
    }
    
    .type-request {
        background-color: #e74c3c;
        color: white;
    }
    
    .no-skills {
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 8px;
        color: #7f8c8d;
    }
    
    .no-skills a {
        color: #3498db;
        text-decoration: none;
        font-weight: bold;
    }
    
    .no-skills a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .skills-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
        
        .skills-container {
            padding: 20px;
        }
    }
</style>

<div class="skills-container">
    <h2>My Posted Skills</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="skills-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="skill-card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['skill_img']); ?>" alt="Skill Image">
                    <div class="skill-card-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="desc"><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
                        <div class="skill-info">
                            <span><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></span>
                            <span class="skill-type type-<?php echo $row['type']; ?>"><?php echo ucfirst($row['type']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-skills">
            <p>You haven't posted any skills yet. <a href="post_skill.php">Post one now</a>!</p>
        </div>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>