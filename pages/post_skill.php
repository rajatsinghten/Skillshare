<?php
include("../includes/db.php");
include("../includes/auth.php");

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $type = $_POST['type'];

    // Handle Image Upload
    $upload_dir = "../uploads/";
    $image = $_FILES['image']['name'];
    $temp = $_FILES['image']['tmp_name'];

    if (!empty($image)) {
        $image_name = time() . "_" . basename($image);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($temp, $image_path)) {
            // Save to DB
            $sql = "INSERT INTO skills (user_id, title, description, skill_img, category, type) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $desc, $image_name, $category, $type);
            mysqli_stmt_execute($stmt);
            $success = "Skill posted successfully!";
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "No image selected.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Skill</title>
    <link rel="stylesheet" href="../assets/css/post_skill.css">
</head>
<body>
<div class="container">
    <h2>Post a New Skill</h2>

    <?php if ($success): ?>
        <p class="message success"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
        <p class="message error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Skill Title</label>
        <input type="text" name="title" required>

        <label for="description">Description</label>
        <textarea name="description" required></textarea>

        <label for="category">Category</label>
        <input type="text" name="category" required>

        <label for="type">Type</label>
        <select name="type" required>
            <option value="offer">I can offer this</option>
            <option value="request">I want to learn this</option>
        </select>

        <label for="image">Skill Image</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Post Skill</button>
    </form>
</div>
</body>
</html>
