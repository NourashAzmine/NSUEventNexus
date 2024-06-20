<?php
include 'connection.php'; // Ensure database connection is included

// Fetch upcoming events
$result = $conn->query("SELECT COUNT(*) FROM events WHERE date > NOW()");
$upcomingEvents = $result->fetch_row()[0];

// Fetch ongoing events
$result = $conn->query("SELECT COUNT(*) FROM events WHERE date = CURDATE()");
$ongoingEvents = $result->fetch_row()[0];

// Fetch past events
$result = $conn->query("SELECT COUNT(*) FROM events WHERE date < NOW()");
$pastEvents = $result->fetch_row()[0];

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NSU Event Nexus</title>
    <link rel="stylesheet" href="CSS/adminDashboardCSS.css"> <!-- Assuming a CSS file to style the dashboard -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="adminDashboard.php" class="navbar-brand">
                    <i class="fas fa-calendar-alt"></i> NSU Event Nexus
                </a>
            </div>
            
            <ul class="navbar-menu">
                <li><a href="adminDashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <!-- <li><a href="create_event.php"><i class="fas fa-plus-circle"></i> Create Event</a></li> -->
                <li><a href="manage_events_by_admin.php"><i class="fas fa-edit"></i> Manage Events</a></li>
                <li><a href="approve_events.php"><i class="fas fa-check-circle"></i> Approve Events</a></li>
                <li><a href="user_list.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>
    

    <div class="container dashboard-container">
    <h1 class="admin-title">Admin Dashboard</h1>

        <h1 class="overview-title">Overview</h1>
        <div class="overview-section">
            <div class="overview-box">
            <p><?php echo $upcomingEvents; ?></p>
                <h2>Upcoming Events</h2>
            </div>
            <div class="overview-box">
            <p><?php echo $ongoingEvents; ?></p>

                <h2>Ongoing Events</h2>
            </div>
            <div class="overview-box">
            <p><?php echo $pastEvents; ?></p>

                <h2>Past Events</h2>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
