<?php
$servername = "localhost";
$username = "root";
$password = "";
$conn = mysqli_connect($servername, $username, $password);
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS wsu_leave_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, "wsu_leave_system");
$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('staff', 'head', 'hr', 'admin') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";
mysqli_query($conn, $users_sql);
$requests_sql = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected', 'Finalized', 'HR_Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB";
mysqli_query($conn, $requests_sql);
$check_evidence = mysqli_query($conn, "SHOW COLUMNS FROM leave_requests LIKE 'evidence_file'");
if (mysqli_num_rows($check_evidence) == 0) {
    mysqli_query($conn, "ALTER TABLE leave_requests ADD COLUMN evidence_file VARCHAR(255) DEFAULT NULL AFTER reason");
}
$check_remark = mysqli_query($conn, "SHOW COLUMNS FROM leave_requests LIKE 'head_remark'");
if (mysqli_num_rows($check_remark) == 0) {
    mysqli_query($conn, "ALTER TABLE leave_requests ADD COLUMN head_remark TEXT DEFAULT NULL AFTER status");
}
mysqli_query($conn, "ALTER TABLE leave_requests MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected', 'Finalized', 'HR_Rejected') DEFAULT 'Pending'");
$notif_sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB";
if (mysqli_query($conn, $notif_sql)) {
    echo "Database, Tables and Remarks are updated successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
mysqli_close($conn);
?>