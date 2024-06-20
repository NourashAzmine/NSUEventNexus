<?php
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['attendeeID'])) {
    $eventID = $_POST['eventID'];
    $attendeeID = $_SESSION['attendeeID'];

    // Check if the attendee is already registered for the event
    $stmt = $conn->prepare("SELECT * FROM eventattendees WHERE eventID = ? AND attendeeID = ?");
    $stmt->bind_param("ii", $eventID, $attendeeID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Register the attendee for the event
        $stmt = $conn->prepare("INSERT INTO eventattendees (eventID, attendeeID) VALUES (?, ?)");
        $stmt->bind_param("ii", $eventID, $attendeeID);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Registered successfully!";
    }
}

$conn->close();
header("Location: index.php");
exit();
?>
