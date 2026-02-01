<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agri_supply_chain";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
