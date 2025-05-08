<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
include_once '../db.php';  // Corrected path to go one level up from the admin folder

// Fetch users
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../css/admin.css"> <!-- Correct path for CSS -->
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_analytics.php">Analytics</a></li>
                <li><a href="user_management.php">User Management</a></li>
                <li><a href="service_management.php">Service Management</a></li>
                <li><a href="../logout.php">Logout</a></li> <!-- Corrected logout path -->
            </ul>
        </div>

        <div class="main-content">
            <h1>User Management</h1>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['role'] ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a> |
                                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
