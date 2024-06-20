<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

$userID = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$userID) {
    header('Location: user_list.php');
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, photoURL FROM users WHERE userID = ? AND userID NOT IN (SELECT adminID FROM admins)");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: user_list.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Handle photo upload
    $photoURL = $user['photoURL']; // Keep the existing photo URL
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "images/uploads/userPhotos/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photoURL = $target_file; // Update with the new photo URL
        }
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, photoURL = ? WHERE userID = ?");
    $stmt->bind_param("sssi", $username, $email, $photoURL, $userID);
    $stmt->execute();
    $stmt->close();

    header('Location: user_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="CSS/editUserCSS.css">
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
                <li><a href="manage_events_by_admin.php"><i class="fas fa-edit"></i> Manage Events</a></li>
                <li><a href="approve_events.php"><i class="fas fa-check-circle"></i> Approve Events</a></li>
                <li><a href="user_list.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container edit-user-container">
        <h1>Edit User</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="form-section">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-section">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-section">
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo">
                <p>Current Photo: <img src="<?php echo htmlspecialchars($user['photoURL']); ?>" alt="User Photo" class="user-photo"></p>
            </div>
            <button type="submit" class="btn">Update User</button>
        </form>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 NSU Event Nexus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
