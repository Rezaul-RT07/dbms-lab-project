<?php include 'db.php';

$buyer_id = 1; // Simulating logged-in Buyer ID 1
$page = $_GET['page'] ?? 'market'; // Default view
$search = $_GET['search'] ?? '';



// 1. Buy Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buy_now'])) {
    $crop_id = $_POST['crop_id'];
    $price = $_POST['price'];
    $order_date = date('Y-m-d');

    // Check if enough quantity exists (Optional robustness)
    // For now, just place order

    $sql = "INSERT INTO Orders (order_date, order_status, total_price, crop_id, buyer_id) VALUES ('$order_date', 'Pending', '$price', '$crop_id', '$buyer_id')";

    if ($conn->query($sql) === TRUE) {
        $msg = "Order placed! Track it in 'My Orders'.";
        $msg_type = "success";
    } else {
        $msg = "Order failed. Try again.";
        $msg_type = "error";
    }
}

// 2. Cancel Order
if (isset($_POST['cancel_order'])) {
    $oid = $_POST['order_id'];
    $conn->query("DELETE FROM Orders WHERE order_id=$oid AND buyer_id=$buyer_id AND order_status='Pending'");
    $msg = "Order cancelled successfully.";
    $msg_type = "warning";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Krishi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .market-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            height: 180px;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2e7d32;
            font-size: 4rem;
        }

        .product-info {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        .farmer-tag {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .price-tag {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3436;
            margin-top: auto;
        }

        .unit {
            font-size: 0.9rem;
            font-weight: 400;
            color: #636e72;
        }

        /* Search Bar */
        .search-bar {
            background: white;
            padding: 10px 20px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-leaf"></i> Krishi Market
        </div>
        <ul class="nav-links">
            <li><a href="?page=market" class="<?php echo $page == 'market' ? 'active' : ''; ?>"><i
                        class="fas fa-store"></i>
                    Browse Crops</a></li>
            <li><a href="?page=orders" class="<?php echo $page == 'orders' ? 'active' : ''; ?>"><i
                        class="fas fa-shopping-basket"></i> My Orders</a></li>
            <li style="margin-top: auto;"><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1><?php echo $page == 'market' ? 'Fresh Market' : 'My Orders'; ?></h1>

            <?php if ($page == 'market'): ?>
                <form action="" method="GET" class="search-bar">
                    <i class="fas fa-search" style="color: #ccc;"></i>
                    <input type="text" name="search" placeholder="Search vegetables (e.g. Potato)..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <!-- Maintain page param -->
                    <?php if ($page != 'market'): ?><input type="hidden" name="page" value="market"><?php endif; ?>
                </form>
            <?php endif; ?>

            <div class="user-profile" style="color: #666;">
                Welcome, Buyer
            </div>
        </header>

        <?php if (isset($msg)): ?>
            <div
                style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- VIEW: MARKETPLACE -->
        <?php if ($page == 'market'): ?>
            <div class="market-grid">
                <?php
                $sql = "SELECT Crop.*, Farmer.name as farmer_name, Farmer.district 
                    FROM Crop 
                    JOIN Farmer ON Crop.farmer_id = Farmer.farmer_id";

                if (!empty($search)) {
                    $sql .= " WHERE Crop.crop_name LIKE '%$search%' OR Farmer.district LIKE '%$search%'";
                }

                $sql .= " ORDER BY Crop.crop_id DESC";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Image Logic
                        $img_src = "";
                        $crop_lower = strtolower($row['crop_name']);

                        if (strpos($crop_lower, 'rice') !== false)
                            $img_src = "images/crop_rice.png";
                        elseif (strpos($crop_lower, 'wheat') !== false)
                            $img_src = "images/crop_wheat.png";
                        elseif (strpos($crop_lower, 'potato') !== false)
                            $img_src = "images/crop_potato.png";
                        elseif (strpos($crop_lower, 'tomato') !== false)
                            $img_src = "images/crop_tomato.png";

                        echo '<div class="product-card">';

                        if ($img_src != "") {
                            echo '  <div class="product-img" style="background-image: url(\'' . $img_src . '\'); background-size: cover; background-position: center;"></div>';
                        } else {
                            // Fallback Icon
                            $icon = "fa-carrot";
                            if (strpos($crop_lower, 'onion') !== false)
                                $icon = "fa-bacon"; // Approximation
                            echo '  <div class="product-img"><i class="fas ' . $icon . '"></i></div>';
                        }

                        echo '  <div class="product-info">';
                        echo '      <div class="product-title">' . $row['crop_name'] . '</div>';
                        echo '      <div class="farmer-tag"><i class="fas fa-user-tag"></i> ' . $row['farmer_name'] . ' • ' . $row['district'] . '</div>';
                        echo '      <div style="font-size: 0.9rem; color: #888; margin-bottom: 10px;"><i class="fas fa-box"></i> Stock: ' . $row['quantity'] . ' kg</div>';
                        echo '      <div style="display: flex; justify-content: space-between; align-items: flex-end;">';
                        echo '          <div class="price-tag">৳' . $row['expected_price'] . '<span class="unit">/kg</span></div>';
                        echo '          <form method="POST">';
                        echo '              <input type="hidden" name="crop_id" value="' . $row['crop_id'] . '">';
                        echo '              <input type="hidden" name="price" value="' . $row['expected_price'] . '">';
                        echo '              <button type="submit" name="buy_now" class="btn"><i class="fas fa-shopping-cart"></i> Buy</button>';
                        echo '          </form>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center; font-size: 1.2rem; color: #888; margin-top: 50px;'>No crops found matching your search.</p>";
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- VIEW: ORDERS -->
        <?php if ($page == 'orders'): ?>
            <div class="table-container">
                <h2>Order History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Farmer</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $osql = "SELECT o.*, c.crop_name, f.name as farmer_name 
                             FROM Orders o
                             JOIN Crop c ON o.crop_id = c.crop_id
                             JOIN Farmer f ON c.farmer_id = f.farmer_id
                             WHERE o.buyer_id = $buyer_id
                             ORDER BY o.order_id DESC";
                        $ores = $conn->query($osql);

                        if ($ores && $ores->num_rows > 0) {
                            while ($r = $ores->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>#" . $r['order_id'] . "</td>";
                                echo "<td>" . $r['crop_name'] . "</td>";
                                echo "<td>" . $r['farmer_name'] . "</td>";
                                echo "<td>৳" . $r['total_price'] . "</td>";
                                echo "<td>" . $r['order_date'] . "</td>";

                                $status_class = 'badge-warning';
                                if ($r['order_status'] == 'Confirmed')
                                    $status_class = 'badge-success';
                                if ($r['order_status'] == 'Completed')
                                    $status_class = 'badge-success';
                                if ($r['order_status'] == 'Rejected')
                                    $status_class = 'badge-danger';

                                echo "<td><span class='badge $status_class'>" . $r['order_status'] . "</span></td>";

                                echo "<td>";
                                if ($r['order_status'] == 'Pending') {
                                    echo "<form method='POST'>
                                        <input type='hidden' name='order_id' value='" . $r['order_id'] . "'>
                                        <button type='submit' name='cancel_order' class='btn' style='background: #e74c3c; padding: 5px 10px; font-size: 0.8rem;'>Cancel</button>
                                      </form>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center;'>You haven't placed any orders yet. <a href='?page=market'>Go Shopping!</a></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="stats-grid" style="margin-top: 30px;">
                <div class="card">
                    <h3>Total Spent</h3>
                    <div class="value">
                        <?php
                        $res = $conn->query("SELECT SUM(total_price) as s FROM Orders WHERE buyer_id = $buyer_id AND order_status IN ('Confirmed', 'Completed')");
                        $val = $res->fetch_assoc()['s'];
                        echo "৳" . number_format($val ?? 0, 2);
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>