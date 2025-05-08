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

    // Prepare the SQL to delete the user
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    
    // Execute the query
    if ($stmt->execute(['id' => $userId])) {
        header('Location: user_management.php'); // Redirect back to user management page
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "No user ID provided.";
}
?>
