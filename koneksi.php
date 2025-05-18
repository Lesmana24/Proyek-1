<?php
$servername = "localhost";
$username = "Admin";
$password = "Admin123";
$dbname = "laundry";

ini_set('memory_limit', '1G');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>