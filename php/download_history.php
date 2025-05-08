<?php
session_start();
require_once 'db.php';
require_once '../vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("Unauthorized access.");
}

$user_email = $_SESSION['user']['email'];

// Fetch user details
$sql = "SELECT id, name FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->execute(['email' => $user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$user_id = $user['id'];
$user_name = $user['name'];

// Fetch service history
$sql = "SELECT service_name, price, created_at FROM service_selection WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start PDF generation
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Branding / Header
$pdf->Cell(0, 10, 'Construction Management System - Service History', 0, 1, 'C');
$pdf->Ln(5);

// User Info
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Name: ' . $user_name, 0, 1);
$pdf->Cell(0, 10, 'Email: ' . $user_email, 0, 1);
$pdf->Cell(0, 10, 'Downloaded: ' . date("Y-m-d H:i:s"), 0, 1);
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Service Name', 1);
$pdf->Cell(40, 10, 'Price ($)', 1);
$pdf->Cell(60, 10, 'Date Selected', 1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 12);
$total = 0;
foreach ($services as $service) {
    $pdf->Cell(70, 10, $service['service_name'], 1);
    $pdf->Cell(40, 10, number_format($service['price'], 2), 1);
    $pdf->Cell(60, 10, date("Y-m-d", strtotime($service['created_at'])), 1);
    $pdf->Ln();
    $total += $service['price'];
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Total', 1);
$pdf->Cell(40, 10, '$' . number_format($total, 2), 1);
$pdf->Cell(60, 10, '', 1);

// Output PDF with user’s name in filename
$filename = strtolower(str_replace(' ', '_', $user_name)) . '_history.pdf';
$pdf->Output('D', $filename); // 'D' forces download

exit;
?>
