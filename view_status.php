<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM leave_requests WHERE user_id = '$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Leave Status</title>
    <link rel="stylesheet" href="css/view_status_style.css">
</head>
<body>
<div class="status-container" style="max-width: 900px; margin: auto;">
    <h2>My Request Status</h2>
    <a href="request_leave.php">Back to Request Form</a>

    <table border="1" width="100%" style="margin-top:20px; border-collapse:collapse;">
        <thead>
            <tr style="background:#f4f4f4;">
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Remark/Message</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['leave_type']; ?></td>
                <td><?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></td>
                <td>
                    <strong><?php echo str_replace('_', ' ', $row['status']); ?></strong>
                </td>
                <td>
                    <?php 
                    if ($row['status'] == 'Pending') {
                        echo "Waiting for Department Head...";
                    } elseif ($row['status'] == 'Approved') {
                        echo "Approved by Head. Waiting for HR Finalization...";
                    } elseif ($row['status'] == 'Rejected') {
                        echo "Denied by Dept Head: " . htmlspecialchars($row['head_remark']);
                    } elseif ($row['status'] == 'Finalized') {
                        echo "Success! <a href='generate_slip.php?id=".$row['id']."'>Download Slip</a>";
                    } elseif ($row['status'] == 'HR_Rejected') {
                        echo "Denied by HR Office.";
                    }
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>