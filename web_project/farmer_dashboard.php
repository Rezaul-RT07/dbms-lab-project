<?php include 'db.php'; 

$farmer_id = 1; // Simulating logged-in farmer

// --- Handle Actions ---

// 1. Add Crop
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_crop'])) {
    $crop_name = $_POST['crop_name'];
    $quantity = $_POST['quantity'];
    $expected_price = $_POST['expected_price'];
    $harvest_date = $_POST['harvest_date'];

    $sql = "INSERT INTO Crop (crop_name, quantity, expected_price, harvest_date, farmer_id) VALUES ('$crop_name', '$quantity', '$expected_price', '$harvest_date', '$farmer_id')";

    if ($conn->query($sql) === TRUE) {
        $msg = "Crop listed available for buyers!";
        $msg_type = "success";
    } else {
        $msg = "Error: " . $conn->error;
        $msg_type = "error";
    }
}

// 2. Delete Crop
if (isset($_GET['delete_crop'])) {
    $id = $_GET['delete_crop'];
    $conn->query("DELETE FROM Crop WHERE crop_id=$id AND farmer_id=$farmer_id");
    $msg = "Listing removed.";
    $msg_type = "warning";
}

// 3. Update Order Status
if (isset($_POST['update_order'])) {
    $status = $_POST['status'];
    $oid = $_POST['order_id'];
    // Verify this order belongs to one of my crops
    $check = $conn->query("SELECT o.order_id FROM Orders o JOIN Crop c ON o.crop_id = c.crop_id WHERE o.order_id=$oid AND c.farmer_id=$farmer_id");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE Orders SET order_status='$status' WHERE order_id=$oid");
        $msg = "Order status updated to $status.";
        $msg_type = "success";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Krishi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .action-btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.8rem; margin-right: 5px; }
        .btn-approve { background: #4CAF50; color: white; }
        .btn-reject { background: #f44336; color: white; }
        .btn-ship { background: #2196F3; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-tractor"></i> Krishi Farmer
        </div>
        <ul class="nav-links">
            <li><a href="#" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="#orders"><i class="fas fa-clipboard-list"></i> Orders</a></li>
            <li><a href="#crops"><i class="fas fa-leaf"></i> My Crops</a></li>
            <li><a href="#"><i class="fas fa-wallet"></i> Earnings</a></li>
            <li style="margin-top: auto;"><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Farmer Dashboard</h1>
            <div class="user-profile" style="display: flex; align-items: center; gap: 10px; color: #555;">
                <span style="font-weight: 600;">Welcome, Farmer</span>
                <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
        </header>

        <?php if (isset($msg)): ?>
            <div style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="card">
                <h3>Active Listings</h3>
                <div class="value">
                    <?php 
                    $res = $conn->query("SELECT COUNT(*) as c FROM Crop WHERE farmer_id = $farmer_id"); 
                    echo $res ? $res->fetch_assoc()['c'] : 0;
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Pending Orders</h3>
                <div class="value">
                    <?php 
                    $res = $conn->query("SELECT COUNT(*) as c FROM Orders o JOIN Crop c ON o.crop_id = c.crop_id WHERE c.farmer_id = $farmer_id AND o.order_status = 'Pending'"); 
                    echo $res ? $res->fetch_assoc()['c'] : 0;
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>Total Earnings</h3>
                <div class="value">
                    <?php 
                    $res = $conn->query("SELECT SUM(o.total_price) as s FROM Orders o JOIN Crop c ON o.crop_id = c.crop_id WHERE c.farmer_id = $farmer_id AND o.order_status = 'Completed'"); 
                    $val = $res->fetch_assoc()['s'];
                    echo "৳" . number_format($val ?? 0, 2);
                    ?>
                </div>
            </div>
        </div>

        <!-- Orders Management -->
        <div class="table-container" id="orders">
            <h2><i class="fas fa-shopping-basket"></i> Incoming Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Item</th>
                        <th>Buyer</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $order_sql = "SELECT o.*, c.crop_name, b.buyer_name FROM Orders o 
                                  JOIN Crop c ON o.crop_id = c.crop_id 
                                  JOIN Buyer b ON o.buyer_id = b.buyer_id 
                                  WHERE c.farmer_id = $farmer_id 
                                  ORDER BY o.order_date DESC";
                    $orders = $conn->query($order_sql);

                    if ($orders->num_rows > 0) {
                        while ($row = $orders->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['order_id'] . "</td>";
                            echo "<td>" . $row['crop_name'] . "</td>";
                            echo "<td>" . $row['buyer_name'] . "</td>";
                            echo "<td>৳" . $row['total_price'] . "</td>";
                            
                            $status_class = 'badge-warning';
                            if($row['order_status'] == 'Confirmed') $status_class = 'badge-success';
                            if($row['order_status'] == 'Completed') $status_class = 'badge-success';
                            if($row['order_status'] == 'Rejected') $status_class = 'badge-danger'; // Need css for this
                            
                            echo "<td><span class='badge $status_class'>" . $row['order_status'] . "</span></td>";
                            
                            echo "<td>";
                            if ($row['order_status'] == 'Pending') {
                                echo "<form method='POST' style='display:inline;'>
                                        <input type='hidden' name='order_id' value='".$row['order_id']."'>
                                        <input type='hidden' name='status' value='Confirmed'>
                                        <button type='submit' name='update_order' class='action-btn btn-approve' title='Accept Order'><i class='fas fa-check'></i></button>
                                      </form>";
                                echo "<form method='POST' style='display:inline;'>
                                        <input type='hidden' name='order_id' value='".$row['order_id']."'>
                                        <input type='hidden' name='status' value='Rejected'>
                                        <button type='submit' name='update_order' class='action-btn btn-reject' title='Reject Order'><i class='fas fa-times'></i></button>
                                      </form>";
                            } elseif ($row['order_status'] == 'Confirmed') {
                                echo "<form method='POST' style='display:inline;'>
                                        <input type='hidden' name='order_id' value='".$row['order_id']."'>
                                        <input type='hidden' name='status' value='Completed'>
                                        <button type='submit' name='update_order' class='action-btn btn-ship' title='Mark as Delivered'><i class='fas fa-truck'></i> Deliver</button>
                                      </form>";
                            } else {
                                echo "<span style='color:green;'><i class='fas fa-check-circle'></i> Done</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No orders received yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add Crop Form -->
        <div class="table-container" style="margin-top: 30px;" id="crops">
            <h2 style="color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-plus-circle"></i> Add New Product
            </h2>
            <form method="POST" action="" style="margin-top: 25px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div class="form-group">
                        <label>Crop Type</label>
                        <select name="crop_name">
                            <option value="Rice">Rice (Paddy)</option>
                            <option value="Wheat">Wheat</option>
                            <option value="Potato">Potato</option>
                            <option value="Jute">Jute</option>
                            <option value="Onion">Onion</option>
                            <option value="Tomato">Tomato</option>
                            <option value="Vegetables Mix">Vegetables Mix</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity (Kg)</label>
                        <input type="number" name="quantity" required placeholder="Available Kg">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div class="form-group">
                        <label>Price per Kg (Tk)</label>
                        <input type="number" step="0.01" name="expected_price" required placeholder="Price">
                    </div>
                    <div class="form-group">
                        <label>Harvest Date</label>
                        <input type="date" name="harvest_date" required>
                    </div>
                </div>
                <button type="submit" name="add_crop" class="btn" style="padding: 12px 30px;"><i class="fas fa-paper-plane"></i> Publish</button>
            </form>
        </div>

        <!-- My Listings -->
        <div class="table-container" style="margin-top: 30px;">
            <h2>My Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Crop</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM Crop WHERE farmer_id = $farmer_id ORDER BY crop_id DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>" . $row['crop_name'] . "</strong></td>";
                            echo "<td>" . $row['quantity'] . " Kg</td>";
                            echo "<td>৳ " . $row['expected_price'] . "</td>";
                            echo "<td>
                                    <a href='?delete_crop=".$row['crop_id']."' onclick='return confirm(\"Delete this listing?\")' class='action-btn btn-reject' style='text-decoration:none;'><i class='fas fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Empty inventory.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>