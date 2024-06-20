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

// Fetch organizer details from users and organizers tables
$stmt = $conn->prepare("SELECT u.username, u.photoURL, o.organizationType, o.organizationName, o.position, o.contactNumber, o.address FROM users u JOIN organizers o ON u.userID = o.organizerID WHERE o.organizerID = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$stmt->bind_result($username, $photoURL, $organizationType, $organizationName, $position, $contactNumber, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard</title>
    <link rel="stylesheet" href="CSS/organizerDashboardCSS.css"> <!-- Assuming a CSS file to style the dashboard -->
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

    <div class="container dashboard-container">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($photoURL); ?>" alt="Profile Photo" class="profile-photo">
            <h2><?php echo htmlspecialchars($username); ?></h2>
            <p><strong>Organization Type:</strong> <?php echo htmlspecialchars($organizationType); ?></p>
            <p><strong>Organization Name:</strong> <?php echo htmlspecialchars($organizationName); ?></p>
            <p><strong>Position:</strong> <?php echo htmlspecialchars($position); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($contactNumber); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <button class="btn" onclick="window.location.href='edit_profile.php'">Change Information</button>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
