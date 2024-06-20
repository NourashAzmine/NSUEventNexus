<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizerID = $_SESSION['organizerID'];

// Fetch volunteers assigned to the organizer
$volunteers = [];
$stmt = $conn->prepare("SELECT v.volunteerID, u.username, v.assignedTasks 
                        FROM volunteers v 
                        JOIN users u ON v.volunteerID = u.userID
                        WHERE v.organizerID = ?");
$stmt->bind_param("i", $organizerID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $volunteers[] = $row;
}
$stmt->close();

// Handle form submission to assign tasks
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $volunteerID = $_POST['volunteerID'];
    $assignedTasks = $_POST['assignedTasks'];

    $stmt = $conn->prepare("UPDATE volunteers SET assignedTasks = ? WHERE volunteerID = ? AND organizerID = ?");
    $stmt->bind_param("sii", $assignedTasks, $volunteerID, $organizerID);
    $stmt->execute();
    $stmt->close();

    header('Location: volunteer_management.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Management</title>
    <link rel="stylesheet" href="CSS/volunteerManagementCSS.css"> <!-- Assuming a CSS file to style the dashboard -->
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
                <li><a href="volunteer_management.php"><i class="fas fa-users"></i> Volunteer Management</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="mcontainer">
        <header>
            <h1>Volunteer Management</h1>
        </header>
        
        <main>
            <table>
                <thead>
                    <tr>
                        <th>Volunteer Name</th>
                        <th>Assigned Tasks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($volunteers) > 0): ?>
                        <?php foreach ($volunteers as $volunteer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($volunteer['username']); ?></td>
                                <td><?php echo htmlspecialchars($volunteer['assignedTasks']); ?></td>
                                <td>
                                    <form method="post" action="volunteer_management.php">
                                        <input type="hidden" name="volunteerID" value="<?php echo $volunteer['volunteerID']; ?>">
                                        <textarea name="assignedTasks"><?php echo htmlspecialchars($volunteer['assignedTasks']); ?></textarea>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No volunteers found to assign tasks.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
