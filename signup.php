<?php
include 'db_connection.php'; 

$msg = "";
$msg_class = "";

if (isset($_POST['register'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'staff'; 
    if ($password !== $confirm_password) {
        $msg = "Error: Passwords do not match!";
        $msg_class = "error";
    } else {
        $check_user = "SELECT id FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $check_user);
        if (mysqli_num_rows($result) > 0) {
            $msg = "Error: This username is already taken!";
            $msg_class = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, username, password, role) 
                    VALUES ('$first_name', '$last_name', '$username', '$hashed_password', '$role')";
            if (mysqli_query($conn, $sql)) {
                $msg = "Registration successful! You can now log in.";
                $msg_class = "success";
            } else {
                $msg = "Registration failed: " . mysqli_error($conn);
                $msg_class = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSU Staff Registration</title>
    <link rel="stylesheet" href="css/signup_style.css">
</head>
<body>
<div class="signup-container">
    <h2>Staff Registration</h2>
    <?php if ($msg != ""): ?>
        <div class="message <?php echo $msg_class; ?>"> <?php echo $msg; ?> </div>
    <?php endif; ?>
    <form action="signup.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <p style="font-size: 12px; color: #777; margin-bottom: 15px;">
            Note: You will be registered as 'Staff' by default.
        </p>
        <button type="submit" name="register">Register Now</button>
        <p class="footer-text">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </form>
</div>

</body>
</html>