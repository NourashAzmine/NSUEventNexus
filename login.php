<?php
session_start();
include 'connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL to fetch user data
    $sql = "SELECT userID, password FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if password matches
        if (password_verify($password, $user['password'])) {
            // Determine user type
            $userID = $user['userID'];

            // Check if the user is an admin
            $adminCheck = $conn->query("SELECT adminID FROM Admins WHERE adminID = $userID");
            if ($adminCheck->num_rows > 0) {
                $_SESSION['isAdmin'] = $userID; // Set session variable
                header("Location: adminDashboard.php"); // Redirect to Admin Dashboard
                exit();
            }

            // Check if the user is an organizer
            $organizerCheck = $conn->query("SELECT organizerID FROM Organizers WHERE organizerID = $userID");
            if ($organizerCheck->num_rows > 0) {
                $_SESSION['organizerID'] = $userID; // Set session variable
                header("Location: organizerDashboard.php"); // Redirect to Organizer Dashboard
                exit();
            }

            // If the user is neither admin nor organizer, redirect to user dashboard
            $_SESSION['userID'] = $userID; // Set session variable
            header("Location: userDashboard.php"); // Redirect to User Dashboard
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
