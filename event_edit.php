<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizerID = $_SESSION['organizerID'];
$eventID = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if (!$eventID) {
    header('Location: manage_events.php');
    exit();
}

// Fetch event details
$stmt = $conn->prepare("SELECT eventName, eventDetails, date, time, duration, location, bannerURL, registrationDeadline, fee, sponsor FROM events WHERE eventID = ? AND organizerID = ?");
$stmt->bind_param("ii", $eventID, $organizerID);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    header('Location: manage_events.php');
    exit();
}

// Fetch seminar or general event details
$isSeminar = false;
$seminar = [];
$generalEvent = [];

$stmt = $conn->prepare("SELECT topic, speakers FROM seminars WHERE eventID = ?");
$stmt->bind_param("i", $eventID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $isSeminar = true;
    $seminar = $result->fetch_assoc();
}
$stmt->close();

if (!$isSeminar) {
    $stmt = $conn->prepare("SELECT type, organizer, expectedAttendance, theme, hasMultipleSessions, equipmentNeeded FROM generalevents WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $generalEvent = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventName = $_POST['eventName'];
    $eventDetails = $_POST['eventDetails'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $duration = $_POST['duration'];
    $location = $_POST['location'];
    $registrationDeadline = $_POST['registrationDeadline'];
    $fee = $_POST['fee'];
    $sponsor = $_POST['sponsor'];

    $bannerURL = $event['bannerURL']; // Keep the existing banner URL

    if (isset($_FILES['banner']) && $_FILES['banner']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "images/uploads/eventBanners/";
        $target_file = $target_dir . basename($_FILES["banner"]["name"]);
        if (move_uploaded_file($_FILES["banner"]["tmp_name"], $target_file)) {
            $bannerURL = $target_file; // Update with the new banner URL
        }
    }

    $stmt = $conn->prepare("UPDATE events SET eventName = ?, eventDetails = ?, date = ?, time = ?, duration = ?, location = ?, bannerURL = ?, registrationDeadline = ?, fee = ?, sponsor = ? WHERE eventID = ? AND organizerID = ?");
    $stmt->bind_param("ssssdsssdsii", $eventName, $eventDetails, $date, $time, $duration, $location, $bannerURL, $registrationDeadline, $fee, $sponsor, $eventID, $organizerID);
    $stmt->execute();
    $stmt->close();

    if ($isSeminar) {
        $topic = $_POST['topic'];
        $speakers = $_POST['speakers'];
        $stmt = $conn->prepare("UPDATE seminars SET topic = ?, speakers = ? WHERE eventID = ?");
        $stmt->bind_param("ssi", $topic, $speakers, $eventID);
        $stmt->execute();
        $stmt->close();
    } else {
        $type = $_POST['type'];
        $organizer = $_POST['organizer'];
        $expectedAttendance = $_POST['expectedAttendance'];
        $theme = $_POST['theme'];
        $hasMultipleSessions = isset($_POST['hasMultipleSessions']) ? 1 : 0;
        $equipmentNeeded = $_POST['equipmentNeeded'];
        $stmt = $conn->prepare("UPDATE generalevents SET type = ?, organizer = ?, expectedAttendance = ?, theme = ?, hasMultipleSessions = ?, equipmentNeeded = ? WHERE eventID = ?");
        $stmt->bind_param("ssisssi", $type, $organizer, $expectedAttendance, $theme, $hasMultipleSessions, $equipmentNeeded, $eventID);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: manage_events.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="CSS/eventEditCSS.css">
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

    <div class="mcontainer">
        <header>
            <h2>Edit Event</h2>
        </header>

        <main>
            <form method="post" enctype="multipart/form-data">
                <div class="form-section">
                    <label for="eventName">Event Name:</label>
                    <input type="text" id="eventName" name="eventName" value="<?php echo htmlspecialchars($event['eventName']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="eventDetails">Event Details:</label>
                    <textarea id="eventDetails" name="eventDetails" required><?php echo htmlspecialchars($event['eventDetails']); ?></textarea>
                </div>
                <div class="form-section">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($event['time']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="duration">Duration (hours):</label>
                    <input type="number" step="0.1" id="duration" name="duration" value="<?php echo htmlspecialchars($event['duration']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="banner">Event Banner:</label>
                    <input type="file" id="banner" name="banner">
                    <p>Current Banner: <?php echo htmlspecialchars($event['bannerURL']); ?></p>
                </div>
                <div class="form-section">
                    <label for="registrationDeadline">Registration Deadline:</label>
                    <input type="date" id="registrationDeadline" name="registrationDeadline" value="<?php echo htmlspecialchars($event['registrationDeadline']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="fee">Fee:</label>
                    <input type="number" step="0.01" id="fee" name="fee" value="<?php echo htmlspecialchars($event['fee']); ?>" required>
                </div>
                <div class="form-section">
                    <label for="sponsor">Sponsor:</label>
                    <input type="text" id="sponsor" name="sponsor" value="<?php echo htmlspecialchars($event['sponsor']); ?>" required>
                </div>

                <?php if ($isSeminar): ?>
                    <h2>Seminar Details</h2>
                    <div class="form-section">
                        <label for="topic">Topic:</label>
                        <input type="text" id="topic" name="topic" value="<?php echo htmlspecialchars($seminar['topic']); ?>" required>
                    </div>
                    <div class="form-section">
                        <label for="speakers">Speakers:</label>
                        <input type="text" id="speakers" name="speakers" value="<?php echo htmlspecialchars($seminar['speakers']); ?>" required>
                    </div>
                <?php else: ?>
                    <div class="form-section">
                        <label for="type">Type:</label>
                        <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($generalEvent['type']); ?>" required>
                    </div>
                    <div class="form-section">
                        <label for="organizer">Organizer:</label>
                        <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($generalEvent['organizer']); ?>" required>
                    </div>
                    <div class="form-section">
                        <label for="expectedAttendance">Expected Attendance:</label>
                        <input type="number" id="expectedAttendance" name="expectedAttendance" value="<?php echo htmlspecialchars($generalEvent['expectedAttendance']); ?>" required>
                    </div>
                    <div class="form-section">
                        <label for="theme">Theme:</label>
                        <input type="text" id="theme" name="theme" value="<?php echo htmlspecialchars($generalEvent['theme']); ?>" required>
                    </div>
                    <div class="form-section">
                        <label for="hasMultipleSessions">Has Multiple Sessions:</label>
                        <input type="checkbox" id="hasMultipleSessions" name="hasMultipleSessions" <?php if ($generalEvent['hasMultipleSessions']) echo 'checked'; ?>>
                    </div>
                    <div class="form-section">
                        <label for="equipmentNeeded">Equipment Needed:</label>
                        <input type="text" id="equipmentNeeded" name="equipmentNeeded" value="<?php echo htmlspecialchars($generalEvent['equipmentNeeded']); ?>" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn">Update Event</button>
            </form>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
