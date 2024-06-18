<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['organizerID'])) {
    header('Location: login.php');
    exit();
}

$organizerID = $_SESSION['organizerID'];
$eventID = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if ($eventID) {
    // Delete from seminars table if exists
    $stmt = $conn->prepare("DELETE FROM seminars WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $stmt->close();

    // Delete from generalevents table if exists
    $stmt = $conn->prepare("DELETE FROM generalevents WHERE eventID = ?");
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $stmt->close();

    // Delete from events table
    $stmt = $conn->prepare("DELETE FROM events WHERE eventID = ? AND organizerID = ?");
    $stmt->bind_param("ii", $eventID, $organizerID);
    $stmt->execute();
    $stmt->close();
}

header('Location: manage_events.php');
exit();
?>
