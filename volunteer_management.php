<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch volunteers
$volunteers = [];
$stmt = $conn->prepare("SELECT v.role, u.username, e.eventName FROM volunteers v JOIN users u ON v.userID = u.userID JOIN events e ON v.eventID = e.eventID WHERE e.organizerID = ? ORDER BY e.date ASC");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $volunteers[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Management</title>
</head>
<body>
    <header>
        <h1>Volunteer Management</h1>
        <a href="organizerDashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <table border="1">
            <tr>
                <th>Event Name</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
            <?php foreach ($volunteers as $volunteer): ?>
            <tr>
                <td><?php echo htmlspecialchars($volunteer['eventName']); ?></td>
                <td><?php echo htmlspecialchars($volunteer['username']); ?></td>
                <td><?php echo htmlspecialchars($volunteer['role']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>
