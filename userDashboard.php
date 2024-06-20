<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['attendeeID'])) {
    header('Location: login.php');
    exit;
}

$attendeeID = $_SESSION['attendeeID'];
$user = null;
$student = null;
$faculty = null;

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, photoURL FROM Users WHERE userID = ?");
$stmt->bind_param("i", $attendeeID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch additional details based on user type
$stmt = $conn->prepare("SELECT type FROM Attendees WHERE attendeeID = ?");
$stmt->bind_param("i", $attendeeID);
$stmt->execute();
$result = $stmt->get_result();
$type = $result->fetch_assoc()['type'];
$stmt->close();

if ($type === 'Student') {
    $stmt = $conn->prepare("SELECT studentID FROM Students WHERE attendeeID = ?");
    $stmt->bind_param("i", $attendeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
} elseif ($type === 'Faculty') {
    $stmt = $conn->prepare("SELECT facultyInitial FROM Faculty WHERE attendeeID = ?");
    $stmt->bind_param("i", $attendeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="CSS/userDashboardCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-calendar-alt"></i> NSU Event Nexus
                </a>
            </div>
            <ul class="navbar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="events.php"><i class="fas fa-calendar-check"></i> Events</a></li>
                <li><a href="seminars.php"><i class="fas fa-chalkboard-teacher"></i> Seminars</a></li>
                <li><a href="past_events.php"><i class="fas fa-history"></i> Past Events</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container dashboard-container">
        <div class="profile-photo">
            <img src="<?php echo htmlspecialchars($user['photoURL']); ?>" alt="Profile Photo">
        </div>
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <?php if ($student): ?>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['studentID']); ?></p>
        <?php elseif ($faculty): ?>
            <p><strong>Faculty Initial:</strong> <?php echo htmlspecialchars($faculty['facultyInitial']); ?></p>
        <?php endif; ?>
        
        <div class="btn-container">
            <a href="index.php" class="btn back-to-home">Back to Home</a>
            <a href="joinedEvents.php" class="btn joined-events">Joined Events</a>
        </div>
        <div class="btn-container">
            <a href="editProfile.php" class="btn change-info">Change Information</a>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
