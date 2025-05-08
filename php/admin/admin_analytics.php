<?php
session_start();
include_once '../db.php';

// Ensure user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch summary data
function fetchSummaryData($conn) {
    $summary = [];

    $stmt1 = $conn->query("SELECT COUNT(*) AS count FROM services");
    $summary['totalServices'] = $stmt1->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt2 = $conn->query("SELECT COUNT(*) AS count FROM users");
    $summary['totalUsers'] = $stmt2->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt3 = $conn->query("SELECT IFNULL(SUM(price), 0) AS sum FROM service_selection");
    $summary['totalRevenue'] = $stmt3->fetch(PDO::FETCH_ASSOC)['sum'];

    return $summary;
}

// Top services
function fetchTopServices($conn) {
    $sql = "SELECT service_name, COUNT(*) AS selection_count
            FROM service_selection
            GROUP BY service_name
            ORDER BY selection_count DESC
            LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Revenue per service
function fetchRevenuePerService($conn) {
    $sql = "SELECT service_name, SUM(price) AS total
            FROM service_selection
            GROUP BY service_name
            ORDER BY total DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Trend data
function fetchTrends($conn) {
    $sql = "SELECT DATE(ss.created_at) as date, SUM(ss.price) as total
            FROM service_selection ss
            WHERE ss.created_at >= CURDATE() - INTERVAL 7 DAY
            GROUP BY DATE(ss.created_at)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$summaryData = fetchSummaryData($conn);
$topServiceData = fetchTopServices($conn);
$revenueData = fetchRevenuePerService($conn);
$trendData = fetchTrends($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin.css">
    <style>
        .analytics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .analytics-table th, .analytics-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .analytics-table th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_analytics.php">Analytics</a></li>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="service_management.php">Service Management</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Analytics Overview</h1>

        <div class="dashboard-row">
            <div class="summary-card">
                <h3>Total Services</h3>
                <p><?= $summaryData['totalServices'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Users</h3>
                <p><?= $summaryData['totalUsers'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <p>$<?= number_format($summaryData['totalRevenue'], 2) ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h3>Top 5 Most Selected Services</h3>
            <?php if (count($topServiceData) > 0): ?>
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Selection Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topServiceData as $service): ?>
                            <tr>
                                <td><?= htmlspecialchars($service['service_name']) ?></td>
                                <td><?= $service['selection_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No services booked yet.</div>
            <?php endif; ?>
        </div>

        <div class="chart-container">
            <h3>Revenue Per Service</h3>
            <?php if (count($revenueData) > 0): ?>
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Total Revenue ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenueData as $service): ?>
                            <tr>
                                <td><?= htmlspecialchars($service['service_name']) ?></td>
                                <td><?= number_format($service['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No revenue data yet.</div>
            <?php endif; ?>
        </div>

        <div class="chart-container">
            <h3>Service Trends (Last 7 Days)</h3>
            <?php if (count($trendData) > 0): ?>
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Revenue ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trendData as $day): ?>
                            <tr>
                                <td><?= $day['date'] ?></td>
                                <td><?= number_format($day['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No trend data for the past 7 days.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
