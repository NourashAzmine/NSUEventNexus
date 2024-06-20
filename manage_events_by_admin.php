<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

// Fetch all events for the admin
$stmt = $conn->prepare("SELECT eventID, eventName, eventDetails, date, time, duration, location, status FROM events ORDER BY date ASC");
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
    <link rel="stylesheet" href="CSS/manageEventsAdminCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="adminDashboard.php" class="navbar-brand">
                    <i class="fas fa-calendar-alt"></i> NSU Event Nexus
                </a>
            </div>
            <ul class="navbar-menu">
                <li><a href="adminDashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="manage_events_by_admin.php"><i class="fas fa-edit"></i> Manage Events</a></li>
                <li><a href="approve_events.php"><i class="fas fa-check-circle"></i> Approve Events</a></li>
                <li><a href="user_list.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="mcontainer">
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
                                    <a href="event_edit_by_admin.php?event_id=<?php echo $event['eventID']; ?>" class="action-edit">Edit</a>
                                    <a href="delete_event_by_admin.php?event_id=<?php echo $event['eventID']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
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
