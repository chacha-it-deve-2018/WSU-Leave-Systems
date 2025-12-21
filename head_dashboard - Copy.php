<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'head') {
    header("Location: login.php");
    exit();
}
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "UPDATE leave_requests SET status = 'Approved' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: head_dashboard.php?msg=Approved");
        exit();
    }
}
if (isset($_POST['reject_now'])) {
    $id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $remark = mysqli_real_escape_string($conn, $_POST['head_remark']);
    
    $sql = "UPDATE leave_requests SET status = 'Rejected', head_remark = '$remark' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: head_dashboard.php?msg=Rejected");
        exit();
    }
}
$sql = "SELECT lr.*, u.first_name, u.last_name FROM leave_requests lr 
        JOIN users u ON lr.user_id = u.id 
        WHERE lr.status = 'Pending' ORDER BY lr.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Head Dashboard - WSU</title>
    <link rel="stylesheet" href="css/head_style.css">
    <script>
        function toggleRejectBox(id) {
            var box = document.getElementById('reject_div_' + id);
            box.style.display = (box.style.display === "none" || box.style.display === "") ? "block" : "none";
        }
    </script>
</head>
<body>
<div class="container" style="max-width: 1200px;">
    <div class="nav-links">
        <h2>Department Head Dashboard</h2>
        <div>
            <a href="head_history.php" class="btn btn-history">Decision History</a>
            <a href="logout.php" style="margin-left:15px; color:#dc3545; text-decoration:none; font-weight:bold;">Logout</a>
        </div>
    </div>
    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-alert">Status: Request <strong><?php echo htmlspecialchars($_GET['msg']); ?></strong> successfully!</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Evidence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></strong></td>
                    <td><span class="type-badge"><?php echo htmlspecialchars($row['leave_type']); ?></span></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                    <td>
                        <div style="font-size:0.85em; color:#555; max-width:150px;">
                            <?php echo htmlspecialchars($row['reason']); ?>
                        </div>
                    </td>
                    <td>
                        <?php if(!empty($row['evidence_file'])): ?>
                            <a href="<?php echo htmlspecialchars($row['evidence_file']); ?>" target="_blank" class="view-file-link">
                                📄 View File
                            </a>
                        <?php else: ?>
                            <small style="color:#999;">No File</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; gap:5px;">
                            <a href="head_dashboard.php?action=approve&id=<?php echo $row['id']; ?>" 
                               class="btn btn-approve" onclick="return confirm('Approve this request?')">Approve</a>
                            
                            <button type="button" class="btn btn-reject" onclick="toggleRejectBox(<?php echo $row['id']; ?>)">Reject</button>
                        </div>

                        <div id="reject_div_<?php echo $row['id']; ?>" class="reject-box" style="display:none; margin-top:10px;">
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <textarea name="head_remark" placeholder="Why are you rejecting this?" required style="width:100%; height:60px;"></textarea>
                                <button type="submit" name="reject_now" class="btn btn-reject" style="width:100%; margin-top:5px;">Confirm Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding: 50px; color: #888;">No pending leave requests available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>