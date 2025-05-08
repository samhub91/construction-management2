<?php
session_start();
include_once 'db.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['services'])) {
        die("No services selected.");
    }

    $services = $_POST['services'];
    $total_price = 0;

    // Step 1: Clear previous service selections
    $delete_query = "DELETE FROM service_selection WHERE user_id = :user_id";
    $stmt = $conn->prepare($delete_query);
    $stmt->execute(['user_id' => $user_id]);

    // Step 2: Prepare insert statements
    $insert_selection = $conn->prepare(
        "INSERT INTO service_selection (user_id, service_name, price) 
         VALUES (:user_id, :service_name, :price)"
    );

    $insert_history = $conn->prepare(
        "INSERT INTO service_history (user_id, service_name, price, date_selected) 
         VALUES (:user_id, :service_name, :price, NOW())"
    );

    foreach ($services as $service) {
        list($service_name, $price) = explode('|', $service);
        $price = floatval($price);
        $total_price += $price;

        // Insert into service_selection
        $insert_selection->execute([
            'user_id' => $user_id,
            'service_name' => $service_name,
            'price' => $price
        ]);

        // Insert into service_history with date_selected = NOW()
        $insert_history->execute([
            'user_id' => $user_id,
            'service_name' => $service_name,
            'price' => $price
        ]);
    }

    // Step 3: Redirect back to home with total
    header("Location: ../home.php?success=1&total=$total_price");
    exit();
}
?>
