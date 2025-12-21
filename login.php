<?php
session_start();
include 'db_connection.php';
$msg = "";
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];
            $user_role = strtolower(trim($user['role']));
            $_SESSION['role'] = $user_role;
            if ($user_role == 'staff') {
                header("Location: request_leave.php");
                exit();
            } 
            elseif ($user_role == 'head') {
                header("Location: head_dashboard.php");
                exit();
            } 
            elseif ($user_role == 'hr') {
                header("Location: hr_dashboard.php");
                exit();
            } 
            else {
                $msg = "Role Error: Your account role '$user_role' is not recognized.";
            }
        } else {
            $msg = "Error: Invalid password!";
        }
    } else {
        $msg = "Error: User account not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSU Login</title>
    <link rel="stylesheet" href="css/Login_style.css">
</head>
<body>
    <div class="login-card">
        <h2>WSU Leave System</h2>
        <p style="margin-bottom: 20px; color: #414142ff;">Sign in to your account</p>
        <?php if ($msg != ""): ?>
            <div class="message error"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p style="margin-top: 25px; font-size: 14px; color: #888;">
            Don't have an account? <a href="signup.php" style="color: #1a2a6c;
             text-decoration: none; font-weight: bold;">Register Here</a>
        </p>
    </div>
</body>
</html>