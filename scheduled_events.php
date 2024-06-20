<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['organizerID']) && !isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

// Fetch approved events
$stmt = $conn->prepare("SELECT eventID, eventName, eventDetails, date, time, duration, location, bannerURL, registrationDeadline, fee, sponsor FROM events WHERE status = 'approved' ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$seminars = [];
$generalEvents = [];

foreach ($events as $event) {
    // Check if the event is a seminar
    $stmt = $conn->prepare("SELECT topic, speakers FROM seminars WHERE eventID = ?");
    $stmt->bind_param("i", $event['eventID']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $seminarDetails = $result->fetch_assoc();
        $seminars[] = array_merge($event, $seminarDetails);
    } else {
        // Check if the event is a general event
        $stmt = $conn->prepare("SELECT type, organizer, expectedAttendance, theme, hasMultipleSessions, equipmentNeeded FROM generalevents WHERE eventID = ?");
        $stmt->bind_param("i", $event['eventID']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $generalEventDetails = $result->fetch_assoc();
            $generalEvents[] = array_merge($event, $generalEventDetails);
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Events</title>
</head>
<body>
    <h1>Scheduled Events</h1>
    
    <h2>Seminars</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Details</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration (hours)</th>
                <th>Location</th>
                <th>Banner</th>
                <th>Registration Deadline</th>
                <th>Fee</th>
                <th>Sponsor</th>
                <th>Topic</th>
                <th>Speakers</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($seminars) > 0): ?>
                <?php foreach ($seminars as $seminar): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($seminar['eventName']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['eventDetails']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['date']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['time']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['duration']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['location']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($seminar['bannerURL']); ?>" alt="Event Banner" style="max-width: 100px; max-height: 100px;"></td>
                        <td><?php echo htmlspecialchars($seminar['registrationDeadline']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['fee']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['sponsor']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['topic']); ?></td>
                        <td><?php echo htmlspecialchars($seminar['speakers']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12">No seminars found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>General Events</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Details</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration (hours)</th>
                <th>Location</th>
                <th>Banner</th>
                <th>Registration Deadline</th>
                <th>Fee</th>
                <th>Sponsor</th>
                <th>Type</th>
                <th>Organizer</th>
                <th>Expected Attendance</th>
                <th>Theme</th>
                <th>Has Multiple Sessions</th>
                <th>Equipment Needed</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($generalEvents) > 0): ?>
                <?php foreach ($generalEvents as $generalEvent): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($generalEvent['eventName']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['eventDetails']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['date']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['time']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['duration']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['location']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($generalEvent['bannerURL']); ?>" alt="Event Banner" style="max-width: 100px; max-height: 100px;"></td>
                        <td><?php echo htmlspecialchars($generalEvent['registrationDeadline']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['fee']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['sponsor']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['type']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['organizer']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['expectedAttendance']); ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['theme']); ?></td>
                        <td><?php echo $generalEvent['hasMultipleSessions'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo htmlspecialchars($generalEvent['equipmentNeeded']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="16">No general events found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
