<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login page if not an admin
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/admin.css">
   
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_analytics.php">Analytics</a></li>
                <li><a href="user_management.php">User Management</a></li>
                <li><a href="service_management.php">Service Management</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Welcome to the Admin Dashboard</h1>
            <p>Here you can view analytics and manage the system.</p>
        </div>
    </div>
</body>
</html>
