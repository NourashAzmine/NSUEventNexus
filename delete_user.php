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

// Ensure the user is not an admin
$stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE adminID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($isAdmin);
$stmt->fetch();
$stmt->close();

if ($isAdmin) {
    header('Location: user_list.php');
    exit();
}

// Delete related records from organizers table
$stmt = $conn->prepare("DELETE FROM organizers WHERE organizerID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

header('Location: user_list.php');
exit();
?>
