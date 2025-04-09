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

<link rel="stylesheet" href="../assets/css/my_skills.css">

<div class="container">
    <h2>My Posted Skills</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="skills-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="skill-card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['skill_img']); ?>" alt="Skill Image">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="desc"><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                    <p><strong>Type:</strong> <?php echo ucfirst($row['type']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You haven’t posted any skills yet. <a href="post_skill.php">Post one now</a>!</p>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>