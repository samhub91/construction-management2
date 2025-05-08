<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
include_once '../db.php';  // Correct path to go one level up from the admin folder

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $serviceId = $_GET['id'];

    // Prepare the SQL to delete the service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = :id");

    // Execute the delete query
    if ($stmt->execute(['id' => $serviceId])) {
        // Redirect to the service management page after successful deletion
        header('Location: service_management.php');
        exit();
    } else {
        echo "Error deleting service.";
    }
} else {
    echo "No service ID provided.";
    exit();
}
