<?php
session_start();
require_once 'connection.php';

// Check if organizer is logged in
if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizer_id = $_SESSION['organizerID'];

// Fetch organizer details from users table
$stmt = $conn->prepare("SELECT u.username, u.email, u.photoURL, o.organizationType, o.organizationName, o.position, o.contactNumber, o.address FROM users u JOIN organizers o ON u.userID = o.organizerID WHERE o.organizerID = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$stmt->bind_result($username, $email, $photoURL, $organizationType, $organizationName, $position, $contactNumber, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>
    <header>
        <h1>Profile Information</h1>
        <a href="organizerDashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <img src="<?php echo htmlspecialchars($photoURL); ?>" alt="Profile Picture">
        <p>Username: <?php echo htmlspecialchars($username); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Organization Type: <?php echo htmlspecialchars($organizationType); ?></p>
        <p>Organization Name: <?php echo htmlspecialchars($organizationName); ?></p>
        <p>Position: <?php echo htmlspecialchars($position); ?></p>
        <p>Contact Number: <?php echo htmlspecialchars($contactNumber); ?></p>
        <p>Address: <?php echo htmlspecialchars($address); ?></p>
    </main>
</body>
</html>
