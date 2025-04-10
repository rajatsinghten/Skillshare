<?php
include("../includes/db.php");
include("../includes/auth.php");
include("../includes/header.php");

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

<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
        border-bottom: 2px solid #ecf0f1;
        padding-bottom: 10px;
    }
    
    .message {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    form {
        display: grid;
        gap: 15px;
    }
    
    label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #2c3e50;
        display: block;
    }
    
    input[type="text"],
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    
    input[type="text"]:focus,
    select:focus,
    textarea:focus {
        border-color: #3498db;
        outline: none;
    }
    
    textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    button[type="submit"] {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    button[type="submit"]:hover {
        background-color: #2980b9;
    }
    
    @media (max-width: 768px) {
        .form-container {
            padding: 20px;
        }
    }
</style>

<div class="form-container">
    <h2>Post a New Skill</h2>

    <?php if ($success): ?>
        <p class="message success"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
        <p class="message error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="title">Skill Title</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" required></textarea>
        </div>

        <div>
            <label for="category">Category</label>
            <input type="text" name="category" required>
        </div>

        <div>
            <label for="type">Type</label>
            <select name="type" required>
                <option value="offer">I can offer this</option>
                <option value="request">I want to learn this</option>
            </select>
        </div>

        <div>
            <label for="image">Skill Image</label>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <button type="submit">Post Skill</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
