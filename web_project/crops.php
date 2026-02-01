<?php include 'db.php';

// Handle Add Crop
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_crop'])) {
    $crop_name = $_POST['crop_name'];
    $quantity = $_POST['quantity'];
    $expected_price = $_POST['expected_price'];
    $harvest_date = $_POST['harvest_date'];
    $farmer_id = $_POST['farmer_id'];

    

    $sql = "INSERT INTO Crop (crop_name, quantity, expected_price, harvest_date, farmer_id) VALUES ('$crop_name', '$quantity', '$expected_price', '$harvest_date', '$farmer_id')";

    if ($conn->query($sql) === TRUE) {
        $message = "New crop production record added!";
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
    <title>Crop Management - AgriChain</title>
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
            <li><a href="crops.php" class="active"><i class="fas fa-wheat"></i> Crops</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Crop Production</h1>
        </header>

        <?php if (isset($message)): ?>
            <div
                style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <h2>Register New Crop</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Crop Name</label>
                        <select name="crop_name">
                            <option value="Rice">Rice (Paddy)</option>
                            <option value="Wheat">Wheat</option>
                            <option value="Potato">Potato</option>
                            <option value="Jute">Jute</option>
                            <option value="Onion">Onion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Farmer (Producer)</label>
                        <select name="farmer_id" required>
                            <option value="">Select Farmer</option>
                            <?php
                            $farmers = $conn->query("SELECT farmer_id, name FROM Farmer");
                            while ($f = $farmers->fetch_assoc()) {
                                echo "<option value='" . $f['farmer_id'] . "'>" . $f['name'] . " (ID: " . $f['farmer_id'] . ")</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Quantity (Kg)</label>
                        <input type="number" name="quantity" required placeholder="Amount in Kg">
                    </div>
                    <div class="form-group">
                        <label>Expected Price (Tk/Kg)</label>
                        <input type="number" step="0.01" name="expected_price" placeholder="Price">
                    </div>
                    <div class="form-group">
                        <label>Harvest Date</label>
                        <input type="date" name="harvest_date" required>
                    </div>
                </div>
                <button type="submit" name="add_crop" class="btn"><i class="fas fa-plus"></i> Add Crop Record</button>
            </form>
        </div>

        <div class="table-container" style="margin-top: 30px;">
            <h2>Crop Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Crop Name</th>
                        <th>Farmer</th>
                        <th>Quantity</th>
                        <th>Price/Kg</th>
                        <th>Harvest Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT Crop.*, Farmer.name as farmer_name FROM Crop LEFT JOIN Farmer ON Crop.farmer_id = Farmer.farmer_id";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['crop_id'] . "</td>";
                            echo "<td>" . $row['crop_name'] . "</td>";
                            echo "<td>" . $row['farmer_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . " Kg</td>";
                            echo "<td>Tk " . $row['expected_price'] . "</td>";
                            echo "<td>" . $row['harvest_date'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No crops found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>