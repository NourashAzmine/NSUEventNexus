<?php
session_start();
require_once 'connection.php';

// Fetch upcoming events
$events = [];
$stmt = $conn->prepare("SELECT eventName, date, time, location, status FROM events WHERE date >= CURDATE() ORDER BY date ASC");
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
    <link rel="stylesheet" href="CSS/upcomingEventsCSS.css">
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
                <li><a href="volunteer_management.php"><i class="fas fa-users"></i> Volunteer Management</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="mcontainer">
        <header>
            <h1>Upcoming Events</h1>
        </header>

        <main>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                                <td><?php echo htmlspecialchars($event['date']); ?></td>
                                <td><?php echo htmlspecialchars($event['time']); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td><?php echo htmlspecialchars($event['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No upcoming events found.</td>
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
