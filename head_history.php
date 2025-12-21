<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'head') {
    header("Location: login.php");
    exit();
}
$sql = "SELECT lr.*, u.first_name, u.last_name FROM leave_requests lr 
        JOIN users u ON lr.user_id = u.id 
        WHERE lr.status != 'Pending' ORDER BY lr.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision History - WSU</title>
    <link rel="stylesheet" href="css/history_style.css">
</head>
<body>
    <div class="container">
        <h3>Approval & Rejection History</h3>
        <a href="head_dashboard.php" class="back-link">← Back to Dashboard</a>
        <table>
            <thead>
                <tr>
                <th>Employee Name</th>
                    <th>Status</th>
                    <th>Head's Remark (Reason)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                        <td class="status-<?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td class="remark-text">
                            <?php
                            echo ($row['status'] == 'Rejected') ? htmlspecialchars($row['head_remark']) : '_'; 
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding: 30px; color: #999;">
                            No history records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>