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
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Unknown';

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

<style>
    .inbox-container {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    h2, h3 {
        color: #2c3e50;
        margin-top: 20px;
    }
    
    h2 {
        text-align: center;
        border-bottom: 2px solid #ecf0f1;
        padding-bottom: 10px;
        color: #3498db;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid #28a745;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid #dc3545;
    }
    
    ul {
        list-style-type: none;
        padding: 0;
        margin: 20px 0;
    }
    
    li {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid #3498db;
    }
    
    li:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    small {
        color: #7f8c8d;
        display: block;
        margin-top: 5px;
    }
    
    hr {
        margin: 30px 0;
        border: 0;
        height: 1px;
        background-image: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,0.1), rgba(0,0,0,0));
    }
    
    form {
        margin-top: 12px;
    }
    
    button {
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        margin-right: 8px;
        transition: background-color 0.3s, transform 0.2s;
    }
    
    button:hover {
        transform: scale(1.05);
    }
    
    button[value="accept"] {
        background-color: #2ecc71;
        color: white;
    }
    
    button[value="accept"]:hover {
        background-color: #27ae60;
    }
    
    button[value="reject"] {
        background-color: #e74c3c;
        color: white;
    }
    
    button[value="reject"]:hover {
        background-color: #c0392b;
    }
    
    .message-text {
        background-color: #f5f5f5;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        font-style: italic;
        border-left: 3px solid #ddd;
    }
    
    .status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 0.85em;
        font-weight: bold;
    }
    
    .status-pending {
        background-color: #f39c12;
        color: white;
    }
    
    .status-accepted {
        background-color: #2ecc71;
        color: white;
    }
    
    .status-rejected {
        background-color: #e74c3c;
        color: white;
    }
    
    .no-requests {
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 8px;
        color: #7f8c8d;
    }
</style>

<div class="inbox-container">
    <h2>📥 Connection Requests</h2>

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
                        <div class="message-text"><?php echo htmlspecialchars($row['message']); ?></div>
                    <?php endif; ?>
                    <small>Received on <?php echo date("M d, Y h:i A", strtotime($row['timestamp'])); ?></small>

                    <form method="POST" action="accept_reject.php">
                        <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="action" value="accept">✅ Accept</button>
                        <button type="submit" name="action" value="reject">❌ Reject</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="no-requests">
            <p>You don't have any pending connection requests.</p>
        </div>
    <?php endif; ?>

    <hr>

    <!-- SENT CONNECTION REQUESTS -->
    <h3>📤 Sent Requests</h3>
    <?php if (mysqli_num_rows($sent_result) > 0): ?>
        <ul>
            <?php while ($row = mysqli_fetch_assoc($sent_result)): ?>
                <li>
                    You sent a request to <strong><?php echo htmlspecialchars($row['receiver_name']); ?></strong>
                    <br>
                    Status: 
                    <span class="status status-<?php echo strtolower($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                    <?php if (!empty($row['message'])): ?>
                        <div class="message-text"><?php echo htmlspecialchars($row['message']); ?></div>
                    <?php endif; ?>
                    <small>Sent on <?php echo date("M d, Y h:i A", strtotime($row['timestamp'])); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="no-requests">
            <p>You haven't sent any connection requests yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once('../includes/footer.php'); ?>