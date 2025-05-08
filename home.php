<?php
session_start();
require_once 'php/db.php'; // Ensure correct path to your db.php file

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "Please log in to view your selected services.";
    exit;
}

$user_email = $_SESSION['user']['email']; // Get the logged-in user's email

// Get the user_id from the users table based on the email
$sql = "SELECT id FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->execute(['email' => $user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $user_id = $user['id'];

    // Fetch the selected services for the user from the service_selection table
    $sql_services = "SELECT ss.service_name, ss.price 
                     FROM service_selection ss 
                     WHERE ss.user_id = :user_id"; // Reference to your service_selection table
    $stmt_services = $conn->prepare($sql_services);
    $stmt_services->execute(['user_id' => $user_id]);

    $selected_services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

    $total_price = 0;
    if ($selected_services) {
        foreach ($selected_services as $service) {
            $total_price += $service['price']; // Accumulate the total price
        }
    }
} else {
    echo "User not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Construction Management System</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.html">Construction Management System</a>
            </div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="services.html">Services</a></li>
                
                <li><a href="contact.html">Contact</a></li>
                <li><a href="php/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
    <section class="dashboard">
        <h1>Welcome back, <?= htmlspecialchars($_SESSION['user']['name']) ?> 👋</h1>
        <p>We're glad to see you again. Here’s what you’ve selected so far:</p>
    </section>

    <section class="user-services">
        <h2>Your Selected Services</h2>
        
        <?php if (!empty($selected_services)): ?>
            <ul>
                <?php foreach ($selected_services as $service): ?>
                    <li><?= htmlspecialchars($service['service_name']) ?> - $<?= number_format($service['price'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
            <h3>Total Price: $<?= number_format($total_price, 2) ?></h3>
        <?php else: ?>
            <p>You have not selected any services. <a href="services.html">Choose services</a></p>
        <?php endif; ?>
    </section>
</main>
<a href="php/download_history.php">Download Service History PDF</a>


    <footer>
        <p>&copy; 2025 Construction Management System</p>
    </footer>
</body>
</html>
