<?php
session_start();
require_once 'connection.php';

// Fetch event details based on the event_id parameter in the query string
$eventID = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

$event = null;
$seminar = null;
$generalEvent = null;

if ($eventID > 0) {
    // Fetch event details
    $stmt = $conn->prepare("SELECT eventID, eventName, eventDetails, date, time, duration, location, bannerURL, registrationDeadline, fee, sponsor FROM events WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    // Fetch seminar details
    $stmt = $conn->prepare("SELECT topic, speakers FROM seminars WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    $seminar = $result->fetch_assoc();
    $stmt->close();

    // Fetch general event details
    $stmt = $conn->prepare("SELECT type, organizer, expectedAttendance, theme, hasMultipleSessions, equipmentNeeded FROM generalevents WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    $generalEvent = $result->fetch_assoc();
    $stmt->close();
}

// Check if the user is logged in
$loggedIn = isset($_SESSION['attendeeID']);
$attendeeID = $loggedIn ? $_SESSION['attendeeID'] : null;

// Check if the user is registered for the event
$registered = false;
if ($loggedIn) {
    $stmt = $conn->prepare("SELECT eventID FROM eventattendees WHERE attendeeID = ? AND eventID = ?");
    $stmt->bind_param("ii", $attendeeID, $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    $registered = $result->num_rows > 0;
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['eventName']); ?> - NSU Event Nexus</title>
    <link rel="stylesheet" href="CSS/viewEventCSS.css">
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
                <li><a href="registerUser.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="userDashboard.php"><i class="fas fa-user"></i>Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="success-message">
        <?php if (isset($_SESSION['success_message'])): ?>
            <?php echo $_SESSION['success_message']; ?>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    </div>

    <div class="container event-detail-container">
        <?php if ($event): ?>
            <h2><?php echo htmlspecialchars($event['eventName']); ?></h2>
            <img src="<?php echo htmlspecialchars($event['bannerURL']); ?>" alt="Event Banner">
            <p><strong>Details:</strong> <?php echo htmlspecialchars($event['eventDetails']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><strong>Fee:</strong> <?php echo htmlspecialchars($event['fee']); ?></p>
            <p><strong>Sponsor:</strong> <?php echo htmlspecialchars($event['sponsor']); ?></p>
            
            

            

            <?php if ($seminar): ?>
                <p><strong>Topic:</strong> <?php echo htmlspecialchars($seminar['topic']); ?></p>
                <p><strong>Speakers:</strong> <?php echo htmlspecialchars($seminar['speakers']); ?></p>
            <?php endif; ?>

            <?php if ($generalEvent): ?>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($generalEvent['type']); ?></p>
                <p><strong>Organizer:</strong> <?php echo htmlspecialchars($generalEvent['organizer']); ?></p>
                <p><strong>Expected Attendance:</strong> <?php echo htmlspecialchars($generalEvent['expectedAttendance']); ?></p>
                <p><strong>Theme:</strong> <?php echo htmlspecialchars($generalEvent['theme']); ?></p>
                <p><strong>Multiple Sessions:</strong> <?php echo $generalEvent['hasMultipleSessions'] ? 'Yes' : 'No'; ?></p>
            <?php endif; ?>
            <div class="countdown-wrapper">
                <div class="countdown-container">
                    <p>Starts in:</p>
                    <div class="countdown" id="eventCountdown"></div>
                </div>
            </div>
            <div class="countdown-wrapper">
                <div class="countdown-container">
                    <p>Registration ends in:</p>
                    <div class="countdown" id="registrationCountdown"></div>
                </div>
            </div>
            <div class="btn-container">
                <?php if ($loggedIn && $registered): ?>
                    <form action="unregisterEvent.php" method="post" class="event-form">
                        <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">
                        <button type="submit" class="btn registered">Registered</button>
                    </form>
                <?php elseif ($event['registrationDeadline'] >= date('Y-m-d')): ?>
                    <form action="registerEvent.php" method="post" class="event-form">
                        <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">
                        <button type="submit" class="btn register">Register</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <a href="index.php" class="back-to-home">Back to Home</a>
        <?php else: ?>
            <p>Event not found.</p>
        <?php endif; ?>
    </div>
    

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            // Show success message
            const messageBox = $('.success-message');
            if (messageBox.text().trim() !== '') {
                messageBox.show().delay(3000).fadeOut('slow');
            }

            // Handle hover effect for registered button
            $(document).on('mouseenter', '.registered', function() {
                $(this).text('Unregister');
            });

            $(document).on('mouseleave', '.registered', function() {
                $(this).text('Registered');
            });

            // Countdown timer
            function countdownTimer(element, endDate) {
                let timer = setInterval(function() {
                    let now = new Date().getTime();
                    let distance = new Date(endDate).getTime() - now;

                    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $(element).html(`
                        <div class="countdown-segment">
                            <span class="number">${days}</span>
                            <span class="label">Days</span>
                        </div>
                        <div class="countdown-segment">
                            <span class="number">${hours}</span>
                            <span class="label">Hours</span>
                        </div>
                        <div class="countdown-segment">
                            <span class="number">${minutes}</span>
                            <span class="label">Minutes</span>
                        </div>
                        <div class="countdown-segment">
                            <span class="number">${seconds}</span>
                            <span class="label">Seconds</span>
                        </div>
                    `);

                    if (distance < 0) {
                        clearInterval(timer);
                        $(element).html("Event Started");
                    }
                }, 1000);
            }

            countdownTimer('#eventCountdown', '<?php echo $event['date'] . ' ' . $event['time']; ?>');
            countdownTimer('#registrationCountdown', '<?php echo $event['registrationDeadline']; ?>');
        });
    </script>
</body>
</html>
