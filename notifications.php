<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizerID = $_SESSION['organizerID'];

// Fetch notifications for the organizer's events
$notifications = [];
$stmt = $conn->prepare("
    SELECT n.message, n.timestamp 
    FROM notifications n 
    JOIN events e ON n.eventID = e.eventID 
    WHERE e.organizerID = ? 
    ORDER BY n.timestamp DESC
");
$stmt->bind_param("i", $organizerID);
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
    <link rel="stylesheet" href="CSS/notificationsCSS.css">
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
            <h1>Notifications</h1>
        </header>

        <main>
            <table>
                <thead>
                    <tr>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $notification): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                <td><?php echo htmlspecialchars($notification['timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No notifications found.</td>
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
