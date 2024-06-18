<?php
session_start();
require_once 'connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch organizer details from users table
$stmt = $conn->prepare("SELECT u.username FROM users u JOIN organizers o ON u.userID = o.organizerID WHERE o.organizerID = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard</title>
</head>
<body>
    <header>
        <h1>Organizer Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        <a href="logout.php">Logout</a>
    </header>

    <aside>
        <nav>
            <ul>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="upcoming_events.php">Upcoming Events</a></li>
                <li><a href="create_event.php">Create Event</a></li>
                <li><a href="manage_events.php">Manage Events</a></li>
                <li><a href="notifications.php">Notifications</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="volunteer_management.php">Volunteer Management</a></li>
            </ul>
        </nav>
    </aside>

    <main>
        <p>Select an option from the menu to get started.</p>
    </main>
</body>
</html>
