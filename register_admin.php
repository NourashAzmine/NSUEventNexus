<?php
include 'connection.php'; // Ensure this path is correct

function uploadPhoto() {
    $target_dir = "images/uploads/adminPhoto/"; // Specific directory for admin photos
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    if(isset($_FILES["photo"]["tmp_name"]) && $_FILES["photo"]["tmp_name"] != '') {
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<p>File is not an image.</p>";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<p>Sorry, file already exists.</p>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 500000) { // 500 KB limit
        echo "<p>Sorry, your file is too large.</p>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<p>Sorry, your file was not uploaded.</p>";
        return false;
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            echo "<p>Sorry, there was an error uploading your file.</p>";
            return false;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $position = $_POST['position'];
    $department = $_POST['department'];

    if ($password !== $confirm_password) {
        echo "<p>Passwords do not match!</p>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
        $photoURL = uploadPhoto(); // Call upload photo function
        $photoURL = $photoURL ?: 'default.jpg'; // Set default image if upload fails or no file provided

        $sql = "INSERT INTO Users (username, email, password, photoURL) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password_hash, $photoURL);

        if ($stmt->execute()) {
            $adminID = $conn->insert_id; // Get the last inserted ID to use as adminID
            $lastLogin = date('Y-m-d H:i:s'); // Set lastLogin to current datetime
            $sql_admin = "INSERT INTO Admins (adminID, position, department, lastLogin) VALUES (?, ?, ?, ?)";
            $stmt_admin = $conn->prepare($sql_admin);
            $stmt_admin->bind_param("isss", $adminID, $position, $department, $lastLogin);
            
            if ($stmt_admin->execute()) {
                echo "<p>Admin registered successfully!</p>";
                header("Location: login.php"); // Redirect to login page
                exit(); // Ensure no further code is executed after the redirect
            } else {
                echo "<p>Error: " . $stmt_admin->error . "</p>";
            }
            $stmt_admin->close();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="CSS/register_adminCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        window.onload = function() {
            setTimeout(function() {
                var message = document.getElementById('message');
                if (message) {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 1000);
                }
            }, 3000); // Hide after 3 seconds

            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function() {
                    let input = this.previousElementSibling;
                    if (input.type === "password") {
                        input.type = "text";
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = "password";
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            });
        };
    </script>
</head>
<body>
    <?php if (isset($message)): ?>
        <div id="message" class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="registration-container">
        <h2>Register as Admin</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
            <div class="input-group">
                <i class="fas fa-briefcase"></i>
                <input type="text" name="position" placeholder="Position" required>
            </div>
            <div class="input-group">
                <i class="fas fa-building"></i>
                <input type="text" name="department" placeholder="Department" required>
            </div>
            <div class="input-group">
                <i class="fas fa-file-image"></i>
                <input type="file" name="photo" id="photo" class="custom-file-input">
            </div>
            <button type="submit" class="btn">Register</button>
            <a href="index.php" class="back-to-home">Back to Home</a>
        </form>
    </div>
</body>
</html>
