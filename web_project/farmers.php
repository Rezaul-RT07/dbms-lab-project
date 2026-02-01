<?php include 'db.php';

// Handle Add Farmer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_farmer'])) {
    $name = $_POST['name'];
    $nid = $_POST['nid'];
    $phone = $_POST['phone'];
    $district = $_POST['district'];
    $upazila = $_POST['upazila'];

    $sql = "INSERT INTO Farmer (name, nid, phone, district, upazila) VALUES ('$name', '$nid', '$phone', '$district', '$upazila')";

    if ($conn->query($sql) === TRUE) {
        $message = "New farmer added successfully!";
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
    <title>Farmers Management - AgriChain</title>
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
            <li><a href="farmers.php" class="active"><i class="fas fa-users"></i> Farmers</a></li>
            <li><a href="crops.php"><i class="fas fa-wheat"></i> Crops</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Farmer Management</h1>
        </header>

        <?php if (isset($message)): ?>
            <div
                style="background: <?php echo $msg_type == 'success' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $msg_type == 'success' ? '#2E7D32' : '#C62828'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <h2>Add New Farmer</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Farmer Name</label>
                        <input type="text" name="name" required placeholder="Ex: Rahim Uddin">
                    </div>
                    <div class="form-group">
                        <label>NID Number</label>
                        <input type="number" name="nid" required placeholder="National ID">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="017...">
                    </div>
                    <div class="form-group">
                        <label>District</label>
                        <input type="text" name="district" placeholder="Ex: Dhaka">
                    </div>
                    <div class="form-group">
                        <label>Upazila</label>
                        <input type="text" name="upazila" placeholder="Ex: Savar">
                    </div>
                </div>
                <button type="submit" name="add_farmer" class="btn"><i class="fas fa-plus"></i> Register Farmer</button>
            </form>
        </div>

        <div class="table-container" style="margin-top: 30px;">
            <h2>Registered Farmers List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>NID</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM Farmer";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['farmer_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['nid'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['upazila'] . ", " . $row['district'] . "</td>";
                            echo "<td><button class='btn' style='padding: 5px 10px; font-size: 0.8rem;'>View</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No farmers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>