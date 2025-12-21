<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}
$id = mysqli_real_escape_string($conn, $_GET['id']);
$msg = "";
if (isset($_POST['update_user'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $update_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', username='$username', role='$role' WHERE id='$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: hr_dashboard.php?msg=UserUpdated");
        exit();
    } else {
        $msg = "Error updating record: " . mysqli_error($conn);
    }
}
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($result);

if (!$user) { die("User not found!"); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - HR Panel</title>
     <style>
        .edit-form-container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .btn-save { background: #28a745; color: white; border: none; width: 100%; padding: 12px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="edit-form-container">
    <h3>Edit User Information</h3>
    <?php if($msg) echo "<p style='color:red;'>$msg</p>"; ?>
    <form method="POST">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
        <label>Username</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        <label>System Role</label>
        <select name="role">
            <option value="staff" <?php if($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
            <option value="head" <?php if($user['role'] == 'head') echo 'selected'; ?>>Department Head</option>
            <option value="hr" <?php if($user['role'] == 'hr') echo 'selected'; ?>>HR Admin</option>
        </select>
        <button type="submit" name="update_user" class="btn-save">Update User Details</button>
        <p style="text-align:center; margin-top:15px;"><a href="hr_dashboard.php">Cancel and Go Back</a></p>
    </form>
</div>

</body>
</html>