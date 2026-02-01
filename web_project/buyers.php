<?php include 'db.php';

// Handle Add Buyer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_buyer'])) {
    $buyer_name = $_POST['buyer_name'];
    $buyer_type = $_POST['buyer_type'];
    $location = $_POST['location'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO Buyer (buyer_name, buyer_type, location, phone) VALUES ('$buyer_name', '$buyer_type', '$location', '$phone')";

    if ($conn->query($sql) === TRUE) {
        $message = "New buyer registered successfully!";
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
    <title>Buyer Management - AgriChain</title>
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
            <h1>Buyer Registry</h1>
        </header>

        <?php if (isset($message)): ?>
            <div
                style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <h2>Register New Buyer</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Buyer Name</label>
                        <input type="text" name="buyer_name" required placeholder="Company or Individual Name">
                    </div>
                    <div class="form-group">
                        <label>Buyer Type</label>
                        <select name="buyer_type">
                            <option value="Wholesaler">Wholesaler</option>
                            <option value="Retailer">Retailer</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Consumer">Direct Consumer</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="phone" required placeholder="017...">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="City/Area">
                    </div>
                </div>
                <button type="submit" name="add_buyer" class="btn"><i class="fas fa-plus"></i> Register Buyer</button>
            </form>
        </div>

        <div class="table-container" style="margin-top: 30px;">
            <h2>Buyer List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM Buyer";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['buyer_id'] . "</td>";
                            echo "<td>" . $row['buyer_name'] . "</td>";
                            echo "<td>" . $row['buyer_type'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['location'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No buyers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>