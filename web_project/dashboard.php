<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriChain - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i> AgriChain
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="farmers.php"><i class="fas fa-users"></i> Farmers</a></li>
            <li><a href="crops.php"><i class="fas fa-wheat"></i> Crops</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard Overview</h1>
            <div class="user-profile">
                <!-- User profile section could go here -->
            </div>
        </header>

        <div class="stats-grid">
            <div class="card">
                <h3>Total Farmers</h3>
                <div class="value">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as count FROM Farmer");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Active Crops</h3>
                <div class="value">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as count FROM Crop");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Total Orders</h3>
                <div class="value">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as count FROM Orders");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Revenue</h3>
                <div class="value">
                    $<?php
                    $result = $conn->query("SELECT SUM(total_price) as total FROM Orders");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo number_format($row['total'] ?? 0, 2);
                    } else {
                        echo "0.00";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h2>Recent Activities</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT order_id, order_date, order_status, total_price FROM Orders ORDER BY order_date DESC LIMIT 5";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['order_id'] . "</td>";
                            echo "<td>" . $row['order_date'] . "</td>";
                            echo "<td><span class='badge badge-success'>" . $row['order_status'] . "</span></td>";
                            echo "<td>$" . number_format($row['total_price'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No recent orders</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>