<?php
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['attendeeID'])) {
    $eventID = $_POST['eventID'];
    $attendeeID = $_SESSION['attendeeID'];

    // Unregister the attendee from the event
    $stmt = $conn->prepare("DELETE FROM eventattendees WHERE eventID = ? AND attendeeID = ?");
    $stmt->bind_param("ii", $eventID, $attendeeID);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success_message'] = "Unregistered successfully!";
}

$conn->close();
header("Location: index.php");
exit();
?>
