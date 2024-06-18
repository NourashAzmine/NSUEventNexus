<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch feedback
$feedbacks = [];
$stmt = $conn->prepare("SELECT f.comments, f.rating, f.timestamp, u.username FROM feedback f JOIN users u ON f.userID = u.userID WHERE f.eventID IN (SELECT eventID FROM events WHERE organizerID = ?) ORDER BY f.timestamp DESC");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback</title>
</head>
<body>
    <header>
        <h1>Feedback</h1>
        <a href="organizerDashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <table border="1">
            <tr>
                <th>Username</th>
                <th>Comments</th>
                <th>Rating</th>
                <th>Timestamp</th>
            </tr>
            <?php foreach ($feedbacks as $feedback): ?>
            <tr>
                <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                <td><?php echo htmlspecialchars($feedback['timestamp']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>
