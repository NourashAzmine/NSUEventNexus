<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

$adminID = $_SESSION['isAdmin'];

// Fetch events for approval
$stmt = $conn->prepare("SELECT eventID, eventName, eventDetails, date, time, duration, location, status, organizerID FROM events WHERE status = 'pending' ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventID = $_POST['event_id'];
    $action = $_POST['action'];
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE events SET status = 'approved' WHERE eventID = ?");
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE events SET status = 'rejected' WHERE eventID = ?");
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $stmt->close();

        // Insert rejection message into notifications table (assuming a notifications table exists)
        $stmt = $conn->prepare("INSERT INTO notifications (eventID, message, senderID) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $eventID, $message, $adminID);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: approve_events.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Events</title>
    <link rel="stylesheet" href="CSS/approveEventsCSS.css"> <!-- Linking the CSS file -->
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
                <!-- <li><a href="create_event.php"><i class="fas fa-plus-circle"></i> Create Event</a></li> -->
                <li><a href="manage_events_by_admin.php"><i class="fas fa-edit"></i>Manage Events</a></li>
                <li><a href="approve_events.php"><i class="fas fa-check-circle"></i> Approve Events</a></li>
                <li><a href="user_list.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container approve-container">
        <h1>Approve Events</h1>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Event Details</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration (hours)</th>
                    <th>Location</th>
                    <th>Organizer ID</th>
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
                            <td><?php echo htmlspecialchars($event['organizerID']); ?></td>
                            <td>
                                <form method="post" action="approve_events.php">
                                    <input type="hidden" name="event_id" value="<?php echo $event['eventID']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                                    <textarea name="message" placeholder="Reason for rejection (optional)"></textarea>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No pending events found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
