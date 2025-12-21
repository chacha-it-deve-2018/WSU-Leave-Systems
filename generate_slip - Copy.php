<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    echo "<h2>Error: Invalid Request.</h2>";
    exit();
}
$request_id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT lr.*, u.first_name, u.last_name, u.role, u.username 
        FROM leave_requests lr 
        JOIN users u ON lr.user_id = u.id 
        WHERE lr.id = '$request_id' AND lr.status = 'Finalized'";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<h2>Error: Slip not found or not yet finalized by HR.</h2>";
    exit();
}

$back_url = ($_SESSION['role'] == 'hr') ? 'hr_dashboard.php' : 'view_status.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Slip - <?php echo htmlspecialchars($data['first_name']); ?></title>
    <link rel="stylesheet" href="css/slip_style.css">
</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom: 30px;">
    <button onclick="window.print()" class="btn-print">🖨️ Print Leave Slip</button>
    
    <a href="<?php echo $back_url; ?>" class="btn-back" style="text-decoration: none; display: inline-block;">Go Back</a>
</div>

<div class="slip-container">
    <div class="header">
        <h1>WOLAYTA SODO UNIVERSITY</h1>
        <p>HUMAN RESOURCE MANAGEMENT DIRECTORATE</p>
        <p>Official Leave Authorization Slip</p>
    </div>
    <div class="slip-title">LEAVE PERMISSION FORM</div>

    <table class="info-table">
        <tr>
            <td class="label">Employee Name:</td>
            <td><strong><?php echo strtoupper(htmlspecialchars($data['first_name'] . " " . $data['last_name'])); ?></strong></td>
        </tr>
        <tr>
            <td class="label">Username / ID:</td>
            <td><?php echo htmlspecialchars($data['username']); ?></td>
        </tr>
        <tr>
            <td class="label">Leave Category:</td>
            <td><?php echo htmlspecialchars($data['leave_type']); ?> Leave</td>
        </tr>
        <tr>
            <td class="label">Period:</td>
            <td>From <strong><?php echo $data['start_date']; ?></strong> To <strong><?php echo $data['end_date']; ?></strong></td>
        </tr>
        <tr>
            <td class="label">Reason:</td>
            <td><?php echo htmlspecialchars($data['reason']); ?></td>
        </tr>
    </table>

    <p style="margin-top: 30px; font-size: 14px; line-height: 1.6; font-style: italic;">
        * Note: This document is electronically generated and carries the digital signature of the HR Directorate. 
        The employee is required to resume duty on the next working day following the end date.
    </p>

    <div class="footer">
        <div class="sig-block">
            <div style="height: 60px;"></div>
            <div class="sig-line">Employee Signature</div>
        </div>

        <div class="sig-block">
            <div class="stamp-circle">
                <div class="stamp-text">
                    WOLAYTA SODO<br>UNIVERSITY<br>HR OFFICE
                </div>
            </div>

            <div class="cursive-signature">
                MR.MISGE
            </div>

            <div style="margin-top: 60px;" class="sig-line">HR Director Signature & Stamp</div>
            <small>Authorized Date: <?php echo date('d-M-Y'); ?></small>
        </div>
    </div>
</div>

</body>
</html>