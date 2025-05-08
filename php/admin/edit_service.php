<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
include_once '../db.php';  // Correct path to go one level up from the admin folder

// Fetch service details for the service to be edited
if (isset($_GET['id'])) {
    $serviceId = $_GET['id'];

    // Fetch the service data
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute(['id' => $serviceId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        echo "Service not found.";
        exit();
    }
} else {
    echo "No service ID provided.";
    exit();
}

// Handle form submission to update the service
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];

    // Update the service in the database
    $stmt = $conn->prepare("UPDATE services SET service_name = :service_name, service_description = :service_description, price = :price WHERE id = :id");
    if ($stmt->execute(['service_name' => $service_name, 'service_description' => $service_description, 'price' => $price, 'id' => $serviceId])) {
        header('Location: service_management.php'); // Redirect back to the service management page
        exit();
    } else {
        echo "Error updating service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
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
            <h1>Edit Service</h1>

            <form method="POST">
                <label for="service_name">Service Name</label>
                <input type="text" name="service_name" value="<?= $service['service_name'] ?>" required>

                <label for="service_description">Service Description</label>
                <textarea name="service_description" required><?= $service['service_description'] ?></textarea>

                <label for="price">Price</label>
                <input type="number" name="price" value="<?= $service['price'] ?>" required>

                <button type="submit">Update Service</button>
            </form>
        </div>
    </div>
</body>
</html>
