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
    <link rel="stylesheet" href="CSS/manageEventsCSS.css"> <!-- Assuming a CSS file to style the page -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="organizerDashboard.php" class="navbar-brand">
                    <i class="fas fa-calendar-alt"></i> NSU Event Nexus
                </a>
            </div>
            <ul class="navbar-menu">
                <li><a href="organizerDashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="upcoming_events.php"><i class="fas fa-calendar-check"></i> Upcoming Events</a></li>
                <li><a href="create_event.php"><i class="fas fa-plus-circle"></i> Create Event</a></li>
                <li><a href="manage_events.php"><i class="fas fa-edit"></i> Manage Events</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <!-- <li><a href="feedback.php"><i class="fas fa-comments"></i> Feedback</a></li> -->
                <li><a href="volunteer_management.php"><i class="fas fa-users"></i> Volunteer Management</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header>
            <h2>Manage Events</h2>
        </header>

        <main>
            <table>
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
                                    <a href="event_edit.php?event_id=<?php echo $event['eventID']; ?>" class="action-edit">Edit</a>
                                    <a href="delete_event.php?event_id=<?php echo $event['eventID']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
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
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
