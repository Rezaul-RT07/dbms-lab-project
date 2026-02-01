<?php include 'db.php';

// Handle Add Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $buyer_id = $_POST['buyer_id'];
    $crop_id = $_POST['crop_id'];
    $order_date = date('Y-m-d'); // Current date
    $order_status = "Pending";


    $crop_res = $conn->query("SELECT expected_price FROM Crop WHERE crop_id = $crop_id");
    if ($crop_res && $crop_res->num_rows > 0) {
        $c_row = $crop_res->fetch_assoc();
        $total_price = $c_row['expected_price']; // Simulating ordering 1 unit/kg or the lot
    } else {
        $total_price = 0;
    }

    $sql = "INSERT INTO Orders (order_date, order_status, total_price, crop_id, buyer_id) VALUES ('$order_date', '$order_status', '$total_price', '$crop_id', '$buyer_id')";

    if ($conn->query($sql) === TRUE) {
        $message = "Order placed successfully!";
        $msg_type = "success";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - AgriChain</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i> AgriChain
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="farmers.php"><i class="fas fa-users"></i> Farmers</a></li>
            <li><a href="crops.php"><i class="fas fa-wheat"></i> Crops</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                <h1>Orders</h1>
                <a href="buyers.php" class="btn" style="background:var(--secondary-color); font-size:0.9rem;">Manage
                    Buyers</a>
            </div>
        </header>

        <?php if (isset($message)): ?>
            <div
                style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="card">
                <h3>Pending Orders</h3>
                <div class="value">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as count FROM Orders WHERE order_status = 'Pending'");
                    echo ($result) ? $result->fetch_assoc()['count'] : "0";
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Completed</h3>
                <div class="value">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as count FROM Orders WHERE order_status = 'Completed'");
                    echo ($result) ? $result->fetch_assoc()['count'] : "0";
                    ?>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h2>Place New Order</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Select Buyer</label>
                        <select name="buyer_id" required>
                            <option value="">Choose Buyer...</option>
                            <?php
                            $buyers = $conn->query("SELECT buyer_id, buyer_name FROM Buyer");
                            while ($b = $buyers->fetch_assoc()) {
                                echo "<option value='" . $b['buyer_id'] . "'>" . $b['buyer_name'] . "</option>";
                            }
                            ?>
                        </select>
                        <small><a href="buyers.php">Register new buyer</a></small>
                    </div>
                    <div class="form-group">
                        <label>Select Crop to Buy</label>
                        <select name="crop_id" required>
                            <option value="">Choose Crop...</option>
                            <?php
                            $crops = $conn->query("SELECT crop_id, crop_name, expected_price FROM Crop");
                            while ($c = $crops->fetch_assoc()) {
                                echo "<option value='" . $c['crop_id'] . "'>" . $c['crop_name'] . " - Tk" . $c['expected_price'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="place_order" class="btn"><i class="fas fa-check"></i> Place Order</button>
            </form>
        </div>

        <div class="table-container" style="margin-top: 30px;">
            <h2>Order History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Buyer</th>
                        <th>Crop</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT Orders.*, Buyer.buyer_name, Crop.crop_name 
                            FROM Orders 
                            LEFT JOIN Buyer ON Orders.buyer_id = Buyer.buyer_id
                            LEFT JOIN Crop ON Orders.crop_id = Crop.crop_id
                            ORDER BY order_date DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['order_id'] . "</td>";
                            echo "<td>" . $row['buyer_name'] . "</td>";
                            echo "<td>" . $row['crop_name'] . "</td>";
                            echo "<td>Tk " . number_format($row['total_price'], 2) . "</td>";
                            echo "<td>" . $row['order_date'] . "</td>";
                            echo "<td><span class='badge badge-warning'>" . $row['order_status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>