<?php
session_start();
include 'db_connection.php';

// 1. HR Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}

// 2. Action Logic for Leave Requests (Finalizing or Rejecting)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'Finalized' : 'HR_Rejected';

    $update_sql = "UPDATE leave_requests SET status = '$status' WHERE id = '$id'";
    mysqli_query($conn, $update_sql);
    header("Location: hr_dashboard.php?msg=RequestUpdated");
    exit();
}

// 3. User Deletion Logic
if (isset($_GET['delete_user'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['delete_user']);
    $del_sql = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $del_sql);
    header("Location: hr_dashboard.php?msg=UserDeleted");
    exit();
}

// 4. SEARCH & FILTER LOGIC
$search_query = "";
$role_filter = "";
$user_sql = "SELECT * FROM users WHERE 1=1";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $user_sql .= " AND (first_name LIKE '%$search_query%' 
                    OR last_name LIKE '%$search_query%' 
                    OR username LIKE '%$search_query%')";
}

if (isset($_GET['role']) && !empty($_GET['role'])) {
    $role_filter = mysqli_real_escape_string($conn, $_GET['role']);
    $user_sql .= " AND role = '$role_filter'";
}

$user_sql .= " ORDER BY id DESC";
$users_res = mysqli_query($conn, $user_sql);

// 5. Fetch Leave Requests (ከዚህ በታች ያለው መስመር 'Rejected' የሚለውን እንዲጨምር ተደርጓል)
$requests_res = mysqli_query($conn, "SELECT lr.*, u.first_name, u.last_name FROM leave_requests lr 
                JOIN users u ON lr.user_id = u.id 
                WHERE lr.status IN ('Approved', 'Rejected', 'Finalized', 'HR_Rejected') ORDER BY lr.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management Dashboard - WSU</title>
    <link rel="stylesheet" href="css/hr_style.css">
    <style>
        .filter-section { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .view-file { color: #007bff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
        .view-file:hover { background: #007bff; color: white; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .finalized { background: #d4edda; color: #155724; }
        .hr_rejected { background: #f8d7da; color: #721c24; }
        .rejected { background: #f8d7da; color: #721c24; } /* Head Reject ያደረገው */
        .approved { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<div class="hr-container" style="max-width: 1300px; margin: auto; padding: 20px;">
    
    <div class="nav-bar" style="display:flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1a2a6c; padding-bottom: 15px;">
        <h2>HR Administrator Dashboard</h2>
        <a href="logout.php" style="color: #dc3545; font-weight: bold; text-decoration: none; border: 1px solid #dc3545; padding: 5px 15px; border-radius: 5px;">Logout</a>
    </div>

    <h3 style="margin-top: 30px; color: #1a2a6c;">Manage Leave Requests (Head Oversight)</h3>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Head's Reason</th>
                <th>Evidence</th>
                <th>Decision</th>
                <th>Action Slip</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($requests_res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($requests_res)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                    <td><small><?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></small></td>
                    <td><small style="color: #666;"><?php echo htmlspecialchars($row['head_remark'] ?: 'No Remark'); ?></small></td>
                    <td>
                        <?php if(!empty($row['evidence_file'])): ?>
                            <a href="<?php echo htmlspecialchars($row['evidence_file']); ?>" target="_blank" class="view-file">📄 View</a>
                        <?php else: ?>
                            <small style="color:gray;">No File</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'Approved' || $row['status'] == 'Rejected'): ?>
                            <a href="hr_dashboard.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-approve" style="padding:4px 8px; font-size:11px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">Finalize</a>
                            <a href="hr_dashboard.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-reject" style="padding:4px 8px; font-size:11px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px;">Reject</a>
                        <?php else: ?>
                            <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                <?php echo str_replace('_', ' ', $row['status']); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'Finalized'): ?>
                            <a href="generate_slip.php?id=<?php echo $row['id']; ?>" class="btn" style="background:#17a2b8; color:white; padding:3px 8px; text-decoration:none; border-radius:4px; font-size:12px;">Print Slip</a>
                        <?php elseif ($row['status'] == 'HR_Rejected'): ?>
                            <span style="color: #dc3545; font-weight: bold; font-size:11px;">HR DENIED</span>
                        <?php else: ?>
                            <span style="color: gray; font-size:11px;">WAITING</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding: 20px;">No leave requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin-top: 50px; color: #1a73e8;">Registered Users (System Management)</h3>
    
    <div class="filter-section">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search by name or username..." value="<?php echo htmlspecialchars($search_query); ?>" style="flex:2; padding:8px; border:1px solid #ddd; border-radius:4px;">
            <select name="role" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px;">
                <option value="">-- All Roles --</option>
                <option value="staff" <?php if($role_filter == 'staff') echo 'selected'; ?>>Staff</option>
                <option value="head" <?php if($role_filter == 'head') echo 'selected'; ?>>Head</option>
                <option value="hr" <?php if($role_filter == 'hr') echo 'selected'; ?>>HR</option>
            </select>
            <button type="submit" style="background:#1a2a6c; color:white; border:none; padding:8px 20px; border-radius:4px; cursor:pointer;">Search</button>
            <a href="hr_dashboard.php" style="align-self:center; font-size:12px; text-decoration:none; color:#666;">Clear</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Reg. Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($users_res) > 0): ?>
                <?php while($user = mysqli_fetch_assoc($users_res)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><span class="status-badge" style="background:#eee; color:#333;"><?php echo strtoupper($user['role']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" style="background:#ffc107; padding:4px 10px; text-decoration:none; border-radius:4px; color:black; font-size:12px;">Edit</a>
                        <a href="hr_dashboard.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Confirm delete?')" style="background:#dc3545; padding:4px 10px; text-decoration:none; border-radius:4px; color:white; font-size:12px;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>