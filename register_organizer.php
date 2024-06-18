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
</head>
<body>
    <h2>Register as Organizer</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <input type="text" name="organizationType" placeholder="Organization Type (e.g., Club, Teacher)" required><br>
        <input type="text" name="organizationName" placeholder="Organization Name" required><br>
        <input type="text" name="position" placeholder="Position" required><br>
        <input type="text" name="contactNumber" placeholder="Contact Number" required><br>
        <input type="text" name="address" placeholder="Address" required><br>
        <input type="file" name="photo" id="photo"><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
