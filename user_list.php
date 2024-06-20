<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

// Fetch all users excluding admins
$stmt = $conn->prepare("SELECT userID, username, email, photoURL FROM users WHERE userID NOT IN (SELECT adminID FROM admins)");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="CSS/userListCSS.css">
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

    <div class="container user-list-container">
        <h1>Manage Users</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($user['photoURL']); ?>" alt="User Photo" class="user-photo"></td>
                            <td>
                                <a href="edit_user.php?user_id=<?php echo $user['userID']; ?>" class="action-edit">Edit</a>
                                <a href="delete_user.php?user_id=<?php echo $user['userID']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
