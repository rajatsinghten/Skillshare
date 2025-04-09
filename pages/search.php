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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Search Skills</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search by title..." value="<?php echo $search; ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="offer" <?php if ($type == 'offer') echo 'selected'; ?>>Offer</option>
            <option value="request" <?php if ($type == 'request') echo 'selected'; ?>>Request</option>
        </select>
        <input type="text" name="category" placeholder="Category..." value="<?php echo $category; ?>">
        <button type="submit">Search</button>
    </form>
    <hr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div>
            <img src="../uploads/<?php echo htmlspecialchars($row['skill_img']); ?>" width="100" alt="Skill Image">
            <h3><a href="view_skill.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
            <p><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
            <p><strong><?php echo ucfirst(htmlspecialchars($row['type'])); ?></strong> | <?php echo htmlspecialchars($row['category']); ?></p>
            <form method="POST" action="inbox.php">
                <input type="hidden" name="to_id" value="<?php echo $row['user_id']; ?>">
                <button type="submit">Connect</button>
            </form>
        </div>
        <hr>
    <?php } ?>
</body>
</html>
