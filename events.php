<?php
session_start();
require_once 'connection.php';

// Fetch approved general events with date and registration deadline
$stmt = $conn->prepare("SELECT ge.eventID, ge.type, ge.organizer, ge.expectedAttendance, ge.theme, ge.hasMultipleSessions, ge.equipmentNeeded, e.date, e.registrationDeadline, e.bannerURL, e.eventName, e.eventDetails 
                        FROM generalevents ge 
                        JOIN events e ON ge.eventID = e.eventID 
                        WHERE e.status = 'approved' 
                        ORDER BY e.date ASC");
$stmt->execute();
$result = $stmt->get_result();
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$stmt->close();

$loggedIn = isset($_SESSION['attendeeID']);
$attendeeID = $loggedIn ? $_SESSION['attendeeID'] : null;

$registeredEvents = [];
if ($loggedIn) {
    $stmt = $conn->prepare("SELECT eventID FROM eventattendees WHERE attendeeID = ?");
    $stmt->bind_param("i", $attendeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $registeredEvents[] = $row['eventID'];
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
    <title>NSU General Events</title>
    <link rel="stylesheet" href="CSS/eventStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
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
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="popup-message" id="popup-message"><?php echo $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    
    <section class="upcoming-events">
        <div class="container">
            <h2>Upcoming General Events</h2>
            <?php
            $currentDate = date('Y-m-d');
            if (count($events) > 0):
                $filteredEvents = array_filter($events, function ($event) use ($currentDate) {
                    return $event['date'] >= $currentDate && $event['registrationDeadline'] >= $currentDate;
                });
            ?>
                <div class="events-list">
                    <?php foreach ($filteredEvents as $event): ?>
                        <div class="event-item">
                            <img src="<?php echo htmlspecialchars($event['bannerURL']); ?>" alt="Event Banner">
                            <div class="event-info">
                                <h3><?php echo htmlspecialchars($event['eventName']); ?></h3>
                                <p><?php echo htmlspecialchars($event['eventDetails']); ?></p>
                                <p><strong>organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
                                <p><strong>Event Type:</strong> <?php echo htmlspecialchars($event['type']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                                <p><strong>Registration Deadline:</strong> <?php echo htmlspecialchars($event['registrationDeadline']); ?></p>
                                <div class="btn-container">
                                    <?php if ($loggedIn && in_array($event['eventID'], $registeredEvents)): ?>
                                        <form action="unregisterEvent.php" method="post" class="event-form">
                                            <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">
                                            <button type="submit" class="btn unregister">Unregister</button>
                                        </form>
                                    <?php elseif ($event['registrationDeadline'] >= $currentDate): ?>
                                        <form action="registerEvent.php" method="post" class="event-form">
                                            <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">
                                            <button type="submit" class="btn register">Register</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="viewEvent.php?event_id=<?php echo $event['eventID']; ?>" class="btn view-details">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No upcoming events found.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.banner-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 2000,
            });

            // Popup message
            const popupMessage = document.getElementById('popup-message');
            if (popupMessage) {
                setTimeout(() => {
                    popupMessage.style.display = 'none';
                }, 3000);
                popupMessage.onclick = () => {
                    popupMessage.style.display = 'none';
                };
            }
        });
    </script>
</body>
</html>
