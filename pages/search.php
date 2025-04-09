<?php
include("../includes/db.php");

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? '';

$query = "SELECT * FROM skills WHERE 1=1";

if ($search) $query .= " AND title LIKE '%$search%'";
if ($type) $query .= " AND type = '$type'";
if ($category) $query .= " AND category LIKE '%$category%'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Skills</title>
    <link rel="stylesheet" href="../assets/css/search.css">
</head>
<body>
<?php include("../includes/header.php"); ?>
<link rel="stylesheet" href="../assets/css/search.css">

<div class="container">
    <h2>🔍 Explore Skills</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by title..." value="<?php echo $search; ?>">
        
        <select name="type">
            <option value="">All Types</option>
            <option value="offer" <?php if ($type == 'offer') echo 'selected'; ?>>Offer</option>
            <option value="request" <?php if ($type == 'request') echo 'selected'; ?>>Request</option>
        </select>

        <input type="text" name="category" placeholder="Category..." value="<?php echo $category; ?>">
        
        <button type="submit">Search</button>
    </form>

    <div class="results">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="skill-card">
                <img src="../uploads/<?php echo htmlspecialchars($row['skill_img']); ?>" alt="Skill Image">
                <div class="skill-info">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="desc"><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
                    <p><strong><?php echo ucfirst($row['type']); ?></strong> | <?php echo htmlspecialchars($row['category']); ?></p>
                    
                    <form method="POST" action="inbox.php">
                        <input type="hidden" name="to_id" value="<?php echo $row['user_id']; ?>">
                        <button type="submit" class="connect-btn">Connect</button>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
</body>
</html>
