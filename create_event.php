<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch organizer details
$stmt = $conn->prepare("SELECT u.username, o.organizationName FROM users u JOIN organizers o ON u.userID = o.organizerID WHERE o.organizerID = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$stmt->bind_result($username, $organizationName);
$stmt->fetch();
$stmt->close();

function uploadBanner() {
    $target_dir = "images/uploads/eventBanners/";
    
    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["banner"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if(isset($_FILES["banner"]["tmp_name"]) && $_FILES["banner"]["tmp_name"] != '') {
        $check = getimagesize($_FILES["banner"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<p>File is not an image.</p>";
            $uploadOk = 0;
        }
    }

    if (file_exists($target_file)) {
        echo "<p>Sorry, file already exists.</p>";
        $uploadOk = 0;
    }

    if ($_FILES["banner"]["size"] > 5000000) {
        echo "<p>Sorry, your file is too large.</p>";
        $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "<p>Sorry, your file was not uploaded.</p>";
        return false;
    } else {
        if (move_uploaded_file($_FILES["banner"]["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            echo "<p>Sorry, there was an error uploading your file.</p>";
            return false;
        }
    }
}

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
    $eventType = $_POST['eventType'];
    $bannerURL = uploadBanner();
    $bannerURL = $bannerURL ?: 'default.jpg';

    // Insert into events table
    $stmt = $conn->prepare("INSERT INTO events (eventName, eventDetails, date, time, duration, location, status, bannerURL, registrationDeadline, fee, sponsor, organizerID) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsssisi", $eventName, $eventDetails, $date, $time, $duration, $location, $bannerURL, $registrationDeadline, $fee, $sponsor, $organizer_id);

    if ($stmt->execute()) {
        $eventID = $conn->insert_id; // Get the last inserted ID to use as eventID

        // Insert into seminars or generalevents table based on eventType
        if ($eventType == 'seminar') {
            $topic = $_POST['topic'];
            $speakers = $_POST['speakers'];
            $stmt_seminar = $conn->prepare("INSERT INTO seminars (eventID, topic, speakers) VALUES (?, ?, ?)");
            $stmt_seminar->bind_param("iss", $eventID, $topic, $speakers);
            $stmt_seminar->execute();
            $stmt_seminar->close();
        } elseif ($eventType == 'general') {
            $type = $_POST['type'];
            $expectedAttendance = $_POST['expectedAttendance'];
            $theme = $_POST['theme'];
            $hasMultipleSessions = isset($_POST['hasMultipleSessions']) ? 1 : 0;
            $equipmentNeeded = $_POST['equipmentNeeded'];
            $stmt_general = $conn->prepare("INSERT INTO generalevents (eventID, type, organizer, expectedAttendance, theme, hasMultipleSessions, equipmentNeeded) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_general->bind_param("issisis", $eventID, $type, $organizationName, $expectedAttendance, $theme, $hasMultipleSessions, $equipmentNeeded);
            $stmt_general->execute();
            $stmt_general->close();
        }
        
        echo "Event created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link rel="stylesheet" href="CSS/createEventCSS.css"> <!-- Assuming a CSS file to style the page -->
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
            <h2>Create New Event</h2>
        </header>

        <main>
            <form action="create_event.php" method="post" enctype="multipart/form-data" class="event-form">
                <div class="form-left">
                    <label for="eventName">Event Name:</label>
                    <input type="text" id="eventName" name="eventName" required><br>

                    <label for="eventDetails">Event Details:</label>
                    <textarea id="eventDetails" name="eventDetails" required></textarea><br>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required><br>

                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required><br>

                    <label for="duration">Duration (hours):</label>
                    <input type="number" step="0.1" id="duration" name="duration" required><br>
                </div>
                <div class="form-middle">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required><br>

                    <label for="registrationDeadline">Registration Deadline:</label>
                    <input type="date" id="registrationDeadline" name="registrationDeadline" required><br>

                    <label for="fee">Fee:</label>
                    <input type="number" step="0.01" id="fee" name="fee" required><br>

                    <label for="sponsor">Sponsor:</label>
                    <input type="text" id="sponsor" name="sponsor"><br>

                    <label for="banner">Banner Image:</label>
                    <input type="file" id="banner" name="banner"><br>

                    <label for="eventType">Event Type:</label>
                    <select id="eventType" name="eventType" required>
                        <option value="seminar" selected>Seminar</option>
                        <option value="general">General Event</option>
                    </select><br>
                </div>
                <div class="form-right">
                    <div id="generalFields" style="display: none;">
                        <label for="type">Type:</label>
                        <input type="text" id="type" name="type"><br>

                        <label for="expectedAttendance">Expected Attendance:</label>
                        <input type="number" id="expectedAttendance" name="expectedAttendance"><br>

                        <label for="theme">Theme:</label>
                        <input type="text" id="theme" name="theme"><br>

                        <label for="hasMultipleSessions">Has Multiple Sessions:</label>
                        <input type="checkbox" id="hasMultipleSessions" name="hasMultipleSessions"><br>

                        <label for="equipmentNeeded">Equipment Needed:</label>
                        <textarea id="equipmentNeeded" name="equipmentNeeded"></textarea><br>
                    </div>

                    <div id="seminarFields">
                        <label for="topic">Topic:</label>
                        <input type="text" id="topic" name="topic"><br>
                        <label for="speakers">Speakers:</label>
                        <textarea id="speakers" name="speakers"></textarea><br>
                    </div>

                    <button type="submit">Create Event</button>
                </div>
            </form>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('eventType').addEventListener('change', function() {
            var generalFields = document.getElementById('generalFields');
            var seminarFields = document.getElementById('seminarFields');
            if (this.value === 'general') {
                generalFields.style.display = 'block';
                seminarFields.style.display = 'none';
            } else if (this.value === 'seminar') {
                generalFields.style.display = 'none';
                seminarFields.style.display = 'block';
            } else {
                generalFields.style.display = 'none';
                seminarFields.style.display = 'none';
            }
        });

        // Trigger change event on page load to display the correct fields
        document.getElementById('eventType').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
