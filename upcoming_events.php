<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch upcoming events
$events = [];
$stmt = $conn->prepare("SELECT eventName, date, time, location, status FROM events WHERE organizerID = ? ORDER BY date ASC");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Events</title>
</head>
<body>
    <header>
        <h1>Upcoming Events</h1>
        <a href="organizerDashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <table border="1">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                <td><?php echo htmlspecialchars($event['date']); ?></td>
                <td><?php echo htmlspecialchars($event['time']); ?></td>
                <td><?php echo htmlspecialchars($event['location']); ?></td>
                <td><?php echo htmlspecialchars($event['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>
