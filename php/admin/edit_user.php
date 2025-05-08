<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
include_once '../db.php';  // Corrected path to go one level up from the admin folder

// Check if user ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} else {
    echo "No user ID provided.";
    exit();
}

// Handle form submission to update user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Update user in the database
    $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
    if ($stmt->execute(['name' => $name, 'email' => $email, 'role' => $role, 'id' => $userId])) {
        header('Location: user_management.php'); // Redirect back to user management page
        exit();
    } else {
        echo "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
                <li><a href="../../logout.php">Logout</a></li> <!-- Corrected logout path -->
            </ul>
        </div>

        <div class="main-content">
            <h1>Edit User</h1>

            <form method="POST">
                <label for="name">Name</label>
                <input type="text" name="name" value="<?= $user['name'] ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" required>

                <label for="role">Role</label>
                <select name="role">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>

                <button type="submit">Update User</button>
            </form>
        </div>
    </div>
</body>
</html>
