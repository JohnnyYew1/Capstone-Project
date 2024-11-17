<?php
$servername = getenv('DB_SERVER') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$dbname = getenv('DB_NAME') ?: "capstone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}
$updateQuery = "
    UPDATE Booking
    SET Status = 'completed'
    WHERE Status = 'active'
    AND TripDate < NOW()
    AND DepartureTime < NOW()
";
$conn->query($updateQuery);
?>