<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
require_once('../includes/header.php');

$user_id = $_SESSION['user_id'];

// Handle connect request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_id'])) {
    $to_id = $_POST['to_id'];

    // Prevent sending connection to self
    if ($to_id != $user_id) {
        $sql = "INSERT INTO messages (from_id, to_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $to_id);
        mysqli_stmt_execute($stmt);
        $message = "Connection request sent!";
    } else {
        $message = "You cannot send a request to yourself.";
    }
}

// Fetch received connection requests
$sql = "SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.from_id = u.id
        WHERE m.to_id = ?
        ORDER BY m.timestamp DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<h2>Inbox</h2>

<?php if (isset($message)): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<?php if (mysqli_num_rows($result) > 0): ?>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li>
                <strong><?php echo htmlspecialchars($row['sender_name']); ?></strong>
                has sent you a connection request.
                <br><small>Received on <?php echo date("M d, Y h:i A", strtotime($row['created_at'])); ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No connection requests yet.</p>
<?php endif; ?>

<?php require_once('../includes/footer.php'); ?>
