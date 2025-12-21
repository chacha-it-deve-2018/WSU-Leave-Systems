<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$msg = "";
$msg_class = "";
if (isset($_POST['submit_request'])) {
    $user_id = $_SESSION['user_id'];
    $confirm_username = mysqli_real_escape_string($conn, $_POST['confirm_username']);
    $confirm_password = $_POST['confirm_password'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    if (strtotime($end_date) < strtotime($start_date)) {
        $msg = "Error: End Date cannot be earlier than the Start Date!";
        $msg_class = "error";
    } else {
        $sql_verify = "SELECT * FROM users WHERE id = '$user_id'";
        $res_verify = mysqli_query($conn, $sql_verify);
        $user_info = mysqli_fetch_assoc($res_verify);
        if ($user_info['username'] === $confirm_username && password_verify($confirm_password, $user_info['password'])) {
            $leave_type = mysqli_real_escape_string($conn, $_POST['leave_type']);
            $reason = mysqli_real_escape_string($conn, $_POST['reason']);
            $file_name = time() . "_" . $_FILES['evidence']['name'];
            $target = "uploads/" . $file_name;
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $target)) {
                $sql_insert = "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason, evidence_file, status) 
                               VALUES ('$user_id', '$leave_type', '$start_date', '$end_date', '$reason', '$target', 'Pending')";
                
                if (mysqli_query($conn, $sql_insert)) {
                    $msg = "Success: Your request has been verified and submitted!";
                    $msg_class = "success";
                } else {
                    $msg = "Database Error: " . mysqli_error($conn);
                    $msg_class = "error";
                }
            } else {
                $msg = "Error: Failed to upload evidence file.";
                $msg_class = "error";
            }
        } else {
            $msg = "Error: Verification failed. Invalid username or password!";
            $msg_class = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Form - WSU</title>
    <link rel="stylesheet" href="css/request_style.css">
    
    <script>
        function validateDates() {
            var start = document.getElementById("start_date").value;
            var end = document.getElementById("end_date").value;

            if (start && end) {
                if (new Date(end) < new Date(start)) {
                    alert("Invalid Date: The End Date cannot be before the Start Date!");
                    document.getElementById("end_date").value = "";
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<body>
<div class="request-card">
    <a href="logout.php" class="logout-link">Logout</a>
    <h2>Leave Request Form</h2>
    <?php if ($msg != ""): ?>
        <div class="message <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateDates()">
        <label>Type of Leave</label>
        <select name="leave_type" required>
            <option value="">-- Select Leave Type --</option>
            <option value="Birth">Birth Leave</option>
            <option value="Sabbatical">Sabbatical Leave</option>
        </select>
        <label>Start Date</label>
        <input type="date" id="start_date" name="start_date" onchange="validateDates()" required>
        <label>End Date</label>
        <input type="date" id="end_date" name="end_date" onchange="validateDates()" required>
        <label>Evidence (PDF/Image)</label>
        <input type="file" name="evidence" required>
        <label>Detailed Reason</label>
        <textarea name="reason" rows="3" placeholder="Explain your reason here..." required></textarea>
        <div class="auth-box">
            <h4>Identity Verification</h4>
            <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Verify your to submit the request.</p>
            <input type="text" name="confirm_username" placeholder="Confirm Username" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit" name="submit_request" class="submit-btn">Verify & Submit Request</button>
        <a href="view_status.php" class="status-link">View My Request Status</a>
    </form>
</div>
</body>
</html>