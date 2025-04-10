<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
require_once('../includes/header.php');

// Debug: Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Unknown';

// Debug: Print session and user ID for verification
echo "<div style='background: #f0f0f0; padding: 10px; margin-bottom: 20px;'>";
echo "<strong>Debug Info:</strong><br>";
echo "Session User ID: " . htmlspecialchars($user_id) . "<br>";
echo "Session Username: " . htmlspecialchars($user_name) . "<br>";
echo "</div>";

// ===========================================
// 📥 FETCH RECEIVED CONNECTION REQUESTS
// ===========================================
$received_sql = "
    SELECT 
        m.id,
        m.from_id,
        m.message,
        m.status,
        m.timestamp,
        u.name AS sender_name
    FROM 
        messages m
    JOIN 
        users u ON m.from_id = u.id
    WHERE 
        m.to_id = ?
        AND m.status = 'pending'
    ORDER BY 
        m.timestamp DESC
";

$received_stmt = mysqli_prepare($conn, $received_sql);
if (!$received_stmt) {
    die("Error preparing received connections query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($received_stmt, "i", $user_id);
if (!mysqli_stmt_execute($received_stmt)) {
    die("Error executing received connections query: " . mysqli_stmt_error($received_stmt));
}

$received_result = mysqli_stmt_get_result($received_stmt);
if (!$received_result) {
    die("Error fetching received connections: " . mysqli_error($conn));
}

// ===========================================
// 📤 FETCH SENT CONNECTION REQUESTS
// ===========================================
$sent_sql = "
    SELECT 
        m.id,
        m.to_id,
        m.message,
        m.status,
        m.timestamp,
        u.name AS receiver_name
    FROM 
        messages m
    JOIN 
        users u ON m.to_id = u.id
    WHERE 
        m.from_id = ?
    ORDER BY 
        m.timestamp DESC
";

$sent_stmt = mysqli_prepare($conn, $sent_sql);
if (!$sent_stmt) {
    die("Error preparing sent connections query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($sent_stmt, "i", $user_id);
if (!mysqli_stmt_execute($sent_stmt)) {
    die("Error executing sent connections query: " . mysqli_stmt_error($sent_stmt));
}

$sent_result = mysqli_stmt_get_result($sent_stmt);
if (!$sent_result) {
    die("Error fetching sent connections: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connection Requests</title>
    <style>
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        small { color: #777; }
        hr { margin: 20px 0; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>

<h2>📥 Inbox</h2>

<!-- Display success and error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="success">
        <?php 
        echo htmlspecialchars($_SESSION['success']); 
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error">
        <?php 
        echo htmlspecialchars($_SESSION['error']); 
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<!-- RECEIVED CONNECTION REQUESTS -->
<h3>🔔 Received Requests</h3>
<?php if (mysqli_num_rows($received_result) > 0): ?>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($received_result)): ?>
            <li>
                <strong><?php echo htmlspecialchars($row['sender_name']); ?></strong> 
                wants to connect with you.
                <?php if (!empty($row['message'])): ?>
                    <br><em>"<?php echo htmlspecialchars($row['message']); ?>"</em>
                <?php endif; ?>
                <br>
                <small>Received on <?php echo date("M d, Y h:i A", strtotime($row['timestamp'])); ?></small>

                <form method="POST" action="accept_reject.php" style="margin-top: 8px;">
                    <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="action" value="accept" style="background: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">✅ Accept</button>
                    <button type="submit" name="action" value="reject" style="background: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;">❌ Reject</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No pending connection requests.</p>
<?php endif; ?>

<hr>

<!-- SENT CONNECTION REQUESTS -->
<h3>📤 Sent Requests</h3>
<?php if (mysqli_num_rows($sent_result) > 0): ?>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($sent_result)): ?>
            <li>
                You sent a request to <strong><?php echo htmlspecialchars($row['receiver_name']); ?></strong>.
                <br>Status: 
                <span style="color: <?php echo ($row['status'] == 'accepted') ? 'green' : (($row['status'] == 'rejected') ? 'red' : 'orange'); ?>">
                    <?php echo ucfirst($row['status']); ?>
                </span>
                <?php if (!empty($row['message'])): ?>
                    <br><em>"<?php echo htmlspecialchars($row['message']); ?>"</em>
                <?php endif; ?>
                <br>
                <small>Sent on <?php echo date("M d, Y h:i A", strtotime($row['timestamp'])); ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No sent requests yet.</p>
<?php endif; ?>

<?php require_once('../includes/footer.php'); ?>
</body>
</html>