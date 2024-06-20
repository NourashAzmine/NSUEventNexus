<?php
session_start();
require_once 'connection.php';

// Fetch approved seminars
$stmt = $conn->prepare("
    SELECT events.eventID, events.eventName, events.eventDetails, events.date, events.registrationDeadline, events.bannerURL, seminars.topic, seminars.speakers
    FROM events
    INNER JOIN seminars ON events.eventID = seminars.eventID
    WHERE events.status = 'approved' AND events.date >= CURDATE()
    ORDER BY events.date ASC
");
$stmt->execute();
$result = $stmt->get_result();
$seminars = [];
while ($row = $result->fetch_assoc()) {
    $seminars[] = $row;
}
$stmt->close();

$loggedIn = isset($_SESSION['attendeeID']);
$attendeeID = $loggedIn ? $_SESSION['attendeeID'] : null;

$registeredSeminars = [];
if ($loggedIn) {
    $stmt = $conn->prepare("SELECT eventID FROM eventattendees WHERE attendeeID = ?");
    $stmt->bind_param("i", $attendeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $registeredSeminars[] = $row['eventID'];
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
    <title>NSU Seminar Nexus</title>
    <link rel="stylesheet" href="CSS\seminarStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-calendar-alt"></i> NSU Seminar Nexus
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

    
    <section class="upcoming-seminars">
        <div class="container">
            <h2>Upcoming Seminars</h2>
            <?php
            $currentDate = date('Y-m-d');
            if (count($seminars) > 0):
                $filteredSeminars = array_filter($seminars, function ($seminar) use ($currentDate) {
                    return $seminar['date'] >= $currentDate && $seminar['registrationDeadline'] >= $currentDate;
                });
            ?>
                <div class="events-list">
                    <?php foreach ($filteredSeminars as $seminar): ?>
                        <div class="event-item">
                            <img src="<?php echo htmlspecialchars($seminar['bannerURL']); ?>" alt="Seminar Banner">
                            <div class="event-info">
                                <h3><?php echo htmlspecialchars($seminar['eventName']); ?></h3>
                                <p><strong>Topic:</strong> <?php echo htmlspecialchars($seminar['topic']); ?></p>
                                <p><strong>Speakers:</strong> <?php echo htmlspecialchars($seminar['speakers']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($seminar['date']); ?></p>
                                <p><strong>Registration Deadline:</strong> <?php echo htmlspecialchars($seminar['registrationDeadline']); ?></p>
                                <div class="btn-container">
                                    <?php if ($loggedIn && in_array($seminar['eventID'], $registeredSeminars)): ?>
                                        <button class="btn registered">Registered</button>
                                    <?php elseif ($seminar['registrationDeadline'] >= $currentDate): ?>
                                        <form action="registerEvent.php" method="post" class="event-form">
                                            <input type="hidden" name="eventID" value="<?php echo $seminar['eventID']; ?>">
                                            <button type="submit" class="btn register">Register</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="viewEvent.php?event_id=<?php echo $seminar['eventID']; ?>" class="btn view-details">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No upcoming seminars found.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Seminar Nexus. All rights reserved.</p>
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
        });
    </script>
</body>
</html>
