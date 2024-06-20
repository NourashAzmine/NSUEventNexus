<?php
include 'connection.php'; // Ensure this path is correct

$message = '';

function uploadPhoto() {
    global $message; // Add this line to access the global $message variable
    $target_dir = "images/uploads/users/"; // Common directory for user photos
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    if(isset($_FILES["photo"]["tmp_name"]) && $_FILES["photo"]["tmp_name"] != '') {
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message .= "<p>File is not an image.</p>";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $message .= "<p>Sorry, file already exists.</p>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 5000000) { 
        $message .= "<p>Sorry, your file is too large.</p>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $message .= "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message .= "<p>Sorry, your file was not uploaded.</p>";
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $message .= "<p>The file ". htmlspecialchars(basename($_FILES["photo"]["name"])). " has been uploaded.</p>";
            return $target_file;
        } else {
            $message .= "<p>Sorry, there was an error uploading your file.</p>";
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $type = $_POST['type'];
    $identifier = $_POST['identifier'];

    if ($password !== $confirm_password) {
        $message .= "<p>Passwords do not match!</p>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
        $photoURL = uploadPhoto(); // Call upload photo function
        $photoURL = $photoURL ?: 'default.jpg'; // Set default image if upload fails or no file provided

        $sql = "INSERT INTO Users (username, email, password, photoURL) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password_hash, $photoURL);

        if ($stmt->execute()) {
            $userID = $conn->insert_id; // Get the last inserted ID to use as attendeeID
            $sql_attendee = "INSERT INTO Attendees (attendeeID, type) VALUES (?, ?)";
            $stmt_attendee = $conn->prepare($sql_attendee);
            $stmt_attendee->bind_param("is", $userID, $type);
            $stmt_attendee->execute();

            if ($type == 'Student') {
                $sql_student = "INSERT INTO Students (attendeeID, studentID) VALUES (?, ?)";
                $stmt_student = $conn->prepare($sql_student);
                $stmt_student->bind_param("ii", $userID, $identifier);
                $stmt_student->execute();
                $message .= "<p>Student registered successfully!</p>";
            } elseif ($type == 'Faculty') {
                $sql_faculty = "INSERT INTO Faculty (attendeeID, facultyInitial) VALUES (?, ?)";
                $stmt_faculty = $conn->prepare($sql_faculty);
                $stmt_faculty->bind_param("is", $userID, $identifier);
                $stmt_faculty->execute();
                $message .= "<p>Faculty registered successfully!</p>";
            }
            $stmt_attendee->close();

            // Redirect to login page after successful registration
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000); // Redirect after 3 seconds
                  </script>";
        } else {
            $message .= "<p>Error: " . $stmt->error . "</p>";
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
    <title>User Registration</title>
    <link rel="stylesheet" href="CSS/registerUserCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function updateIdentifierInput() {
            var typeSelect = document.getElementById('type');
            var identifierInput = document.getElementById('identifier');
            if (typeSelect.value == "Student") {
                identifierInput.placeholder = "Student ID";
            } else {
                identifierInput.placeholder = "Faculty Initial";
            }
        }

        window.onload = function() {
            updateIdentifierInput(); // Set placeholder on page load
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
        <h2>Register as a User</h2>
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
                <i class="fas fa-id-badge"></i>
                <select name="type" id="type" onchange="updateIdentifierInput()">
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                </select>
            </div>
            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="identifier" id="identifier" placeholder="Student ID or Faculty Initial" required>
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
