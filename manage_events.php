<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizerID = $_SESSION['organizerID'];

// Fetch events for the organizer
$stmt = $conn->prepare("SELECT eventID, eventName, eventDetails, date, time, duration, location, status FROM events WHERE organizerID = ? ORDER BY date ASC");
$stmt->bind_param("i", $organizerID);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
</head>
<body>
    <h1>Manage Events</h1>
    <a href="create_event.php">Create New Event</a>
    <table border="1">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Details</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration (hours)</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                        <td><?php echo htmlspecialchars($event['eventDetails']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['time']); ?></td>
                        <td><?php echo htmlspecialchars($event['duration']); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td><?php echo htmlspecialchars($event['status']); ?></td>
                        <td>
                            <a href="event_edit.php?event_id=<?php echo $event['eventID']; ?>">Edit</a>
                            <a href="delete_event.php?event_id=<?php echo $event['eventID']; ?>" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No events found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
