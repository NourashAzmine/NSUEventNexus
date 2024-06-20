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
            $attendeeCheck = $conn->query("SELECT attendeeID FROM attendees WHERE attendeeID = $userID");
            if ($attendeeCheck->num_rows > 0) {
                $_SESSION['attendeeID'] = $userID; // Set session variable
                header("Location: index.php"); // Redirect to Index Page
                exit();
            }

            // If the user is neither admin nor organizer, redirect to user dashboard
            $_SESSION['userID'] = $userID; // Set session variable
            header("Location: userDashboard.php"); // Redirect to User Dashboard
            // If the user is not found in any table, show an error
            $error = "User role not recognized!";
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
    <link rel="stylesheet" href="CSS/loginCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
                <button type="button" class="eye-icon" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="login.php" class="forgot-password">Forgot Password?</a>
        <a href="index.php" class="back-to-home">Back to Home</a>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('input[name="password"]');

        togglePassword.addEventListener('click', function (e) {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // Toggle the eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
