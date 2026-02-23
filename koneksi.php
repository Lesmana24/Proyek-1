
<?php
$servername = "localhost";
$username = "";
$password = "";
$dbname = "laundry";

ini_set('memory_limit', '1G');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>