<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
include_once '../db.php';  // Corrected path to go one level up from the admin folder

// Fetch services from the database
$services = $conn->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to add a new service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];

    // Insert the new service into the database
    $stmt = $conn->prepare("INSERT INTO services (service_name, service_description, price) VALUES (:service_name, :service_description, :price)");
    if ($stmt->execute(['service_name' => $service_name, 'service_description' => $service_description, 'price' => $price])) {
        header('Location: service_management.php'); // Redirect to the same page to refresh the list
        exit();
    } else {
        echo "Error adding service.";
    }
}

// Handle deletion of a service
if (isset($_GET['delete_id'])) {
    $serviceId = $_GET['delete_id'];

    // Prepare the SQL to delete the service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = :id");
    if ($stmt->execute(['id' => $serviceId])) {
        header('Location: service_management.php'); // Redirect back to the service management page
        exit();
    } else {
        echo "Error deleting service.";
    }
}

// Handle updating an existing service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_service'])) {
    $service_id = $_POST['service_id'];
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];

    // Update the service in the database
    $stmt = $conn->prepare("UPDATE services SET service_name = :service_name, service_description = :service_description, price = :price WHERE id = :id");
    if ($stmt->execute(['service_name' => $service_name, 'service_description' => $service_description, 'price' => $price, 'id' => $service_id])) {
        header('Location: service_management.php'); // Redirect to the same page to refresh the list
        exit();
    } else {
        echo "Error updating service.";
    }
}

// Fetch the service details if editing a specific service
$editing_service = null;
if (isset($_GET['edit_id'])) {
    $service_id = $_GET['edit_id'];
    $editing_service = $conn->query("SELECT * FROM services WHERE id = $service_id")->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management</title>
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
            <h1>Service Management</h1>

            <!-- Form to add or update a service -->
            <h2><?= $editing_service ? 'Edit Service' : 'Add New Service' ?></h2>
            <form method="POST">
                <input type="hidden" name="service_id" value="<?= $editing_service['id'] ?? '' ?>">

                <label for="service_name">Service Name</label>
                <input type="text" name="service_name" value="<?= $editing_service['service_name'] ?? '' ?>" required>

                <label for="service_description">Service Description</label>
                <textarea name="service_description" required><?= $editing_service['service_description'] ?? '' ?></textarea>

                <label for="price">Price</label>
                <input type="number" name="price" value="<?= $editing_service['price'] ?? '' ?>" required>

                <button type="submit" name="<?= $editing_service ? 'update_service' : 'add_service' ?>">
                    <?= $editing_service ? 'Update Service' : 'Add Service' ?>
                </button>
            </form>

            <h2>Existing Services</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td><?= $service['service_name'] ?></td>
                            <td><?= $service['service_description'] ?></td>
                            <td><?= $service['price'] ?></td>
                            <td>
                                <a href="?edit_id=<?= $service['id'] ?>">Edit</a> |
                                <a href="?delete_id=<?= $service['id'] ?>" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
