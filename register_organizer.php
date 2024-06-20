<?php
include 'connection.php'; // Ensure this path is correct

function uploadPhoto() {
    $target_dir = "images/uploads/organizerPhoto/"; // Adjusted upload directory
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
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<p>Sorry, your file was not uploaded.</p>";
        return false;
    // If everything is ok, try to upload file
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
    $organizationType = $_POST['organizationType'];
    $organizationName = $_POST['organizationName'];
    $position = $_POST['position'];
    $contactNumber = $_POST['contactNumber'];
    $address = $_POST['address'];

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
            $organizerID = $conn->insert_id; // Get the last inserted ID to use as organizerID
            $sql_organizer = "INSERT INTO Organizers (organizerID, organizationType, organizationName, position, contactNumber, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_organizer = $conn->prepare($sql_organizer);
            $stmt_organizer->bind_param("isssss", $organizerID, $organizationType, $organizationName, $position, $contactNumber, $address);
            
            if ($stmt_organizer->execute()) {
                echo "<p>Organizer registered successfully!</p>";
                header("Location: login.php"); // Redirect to login page
                exit(); // Ensure no further code is executed after the redirect
            } else {
                echo "<p>Error: " . $stmt_organizer->error . "</p>";
            }
            $stmt_organizer->close();
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
    <title>Organizer Registration</title>
    <link rel="stylesheet" href="CSS/register_organizerCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function togglePasswordVisibility(inputId, toggleIconId) {
            var input = document.getElementById(inputId);
            var toggleIcon = document.getElementById(toggleIconId);
            if (input.type === "password") {
                input.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</head>
<body>
    <?php if (isset($message)): ?>
        <div id="message" class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="registration-container">
        <h2>Register as Organizer</h2>
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
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-eye toggle-password" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')"></i>
            </div>
            <div class="input-group">
                <i class="fas fa-id-badge"></i>
                <input type="text" name="organizationType" placeholder="Organization Type (e.g., Club, Teacher)" required>
            </div>
            <div class="input-group">
                <i class="fas fa-building"></i>
                <input type="text" name="organizationName" placeholder="Organization Name" required>
            </div>
            <div class="input-group">
                <i class="fas fa-briefcase"></i>
                <input type="text" name="position" placeholder="Position" required>
            </div>
            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="text" name="contactNumber" placeholder="Contact Number" required>
            </div>
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="address" placeholder="Address" required>
            </div>
            <div class="input-group">
                <i class="fas fa-file-image"></i>
                <input type="file" name="photo" id="photo" class="custom-file-input">
            </div>
            <button type="submit" class="btn">Register</button>
            <a href="index.php" class="back-to-home">Back to Home</a>
        </form>
    </div>

    <script>
        setTimeout(function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 1000);
            }
        }, 3000); // Hide after 3 seconds
    </script>
</body>
</html>
