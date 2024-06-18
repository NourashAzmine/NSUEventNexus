<?php
include 'connection.php'; // Ensure database connection is included

// Fetch upcoming events
$result = $conn->query("SELECT COUNT(*) FROM Events WHERE date > NOW()");
$upcomingEvents = $result->fetch_row()[0];

// Fetch ongoing events
$result = $conn->query("SELECT COUNT(*) FROM Events WHERE date = CURDATE()");
$ongoingEvents = $result->fetch_row()[0];

// Fetch past events
$result = $conn->query("SELECT COUNT(*) FROM Events WHERE date < NOW()");
$pastEvents = $result->fetch_row()[0];


// Close database connection
$conn->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assuming a CSS file to style the dashboard -->
</head>
<body>
    <h1>Admin Dashboard</h1>
    <div class="overview-section">
        <h2>Overview</h2>
        <p>Upcoming Events: <?php echo $upcomingEvents; ?></p>
        <p>Ongoing Events: <?php echo $ongoingEvents; ?></p>
        <p>Past Events: <?php echo $pastEvents; ?></p>
    </div>

    <div class="event-management">
        <h2>Event Management</h2>
        <ul>
            <li><a href="create_event.php">Create New Event</a></li>
            <li><a href="edit_events.php">Edit Existing Events</a></li>
            <li><a href="approve_events.php">Approve Events</a></li>
            <li><a href="event_schedule.php">Event Scheduling</a></li>
        </ul>
    </div>

    <div class="user-management">
        <h2>User Management</h2>
        <ul>
            <li><a href="user_list.php">Manage Users</a></li>
            <li><a href="role_assignments.php">Role Assignments</a></li>
        </ul>
    </div>

    <div class="reports-analytics">
        <h2>Reports and Analytics</h2>
        <ul>
            <li><a href="event_analytics.php">Event Analytics</a></li>
        </ul>
    </div>

    <div class="communication-tools">
        <h2>Communication Tools</h2>
        <ul>
            <li><a href="manage_announcements.php">Manage Announcements</a></li>
            <li><a href="feedback_support.php">Feedback and Support</a></li>
        </ul>
    </div>

</body>
</html>
