<?php
include("../includes/db.php");
include("../includes/auth.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $type = $_POST['type'];

    // Handle Image Upload
    $image = $_FILES['image']['name'];
    $temp = $_FILES['image']['tmp_name'];
    $upload_dir = "../uploads/";
    $image_path = $upload_dir . basename($image);
    move_uploaded_file($temp, $image_path);

    // Save to DB
    $sql = "INSERT INTO skills (user_id, title, description, image, category, type) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $desc, $image, $category, $type);
    mysqli_stmt_execute($stmt);

    $success = "Skill posted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Skill</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Post a New Skill</h2>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Skill Title" required><br><br>
        <textarea name="description" placeholder="Description" required></textarea><br><br>
        <input type="text" name="category" placeholder="Category" required><br><br>
        <select name="type" required>
            <option value="offer">I can offer this</option>
            <option value="request">I want to learn this</option>
        </select><br><br>
        <input type="file" name="image" accept="image/*" required><br><br>
        <button type="submit">Post Skill</button>
    </form>
</body>
</html>
