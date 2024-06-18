<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch notifications
$notifications = [];
$stmt = $conn->prepare("SELECT message, date FROM notifications WHERE organizerID = ? ORDER BY date DESC LIMIT 5");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
</head>
<body>
    <header>
        <h1>Notifications</h1>
        <a href="organizerDashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <ul>
            <?php foreach ($notifications as $notification): ?>
            <li><?php echo htmlspecialchars($notification['date']) . ': ' . htmlspecialchars($notification['message']); ?></li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
