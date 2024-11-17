<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $licensePlate = $_POST['LicensePlate'];
    $vehicleType = $_POST['VehicleType'];
    $capacity = $_POST['Capacity'];

    $query = "INSERT INTO vehicle (LicensePlate, VehicleType, Capacity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $licensePlate, $vehicleType, $capacity);

    if ($stmt->execute()) {

        $vehicleID = $stmt->insert_id;

        $seatQuery = "INSERT INTO Seats (LicensePlate, SeatNumber) VALUES (?, ?)";
        $seatStmt = $conn->prepare($seatQuery);

        for ($seatNumber = 1; $seatNumber <= $capacity; $seatNumber++) {
            $seatStmt->bind_param("si", $licensePlate, $seatNumber);
            $seatStmt->execute();
        }

        $seatStmt->close();
        $_SESSION['message'] = "Vehicle Added Sucessfully";
        $_SESSION['error'] = false;
        header("Location: vehicle_management.php");
    exit();
    } else {
        $_SESSION['message'] = "Failed to add Vehicle";
        $_SESSION['error'] = false;
        header("Location: vehicle_management.php");
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_user.css">
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Add Vehicle</h1>

        <form action="add_vehicles.php" method="POST">
            <label for="LicensePlate">License Plate:</label>
            <input type="text" id="LicensePlate" name="LicensePlate" required>

            <label for="VehicleType">Vehicle Type:</label>
            <input type="text" id="VehicleType" name="VehicleType" required>

            <label for="Capacity">Capacity:</label>
            <select id="Capacity" name="Capacity" required>
                <option value="13">13</option>
                <option value="16">16</option>
            </select>

            <br><br>
            <input type="submit" value = "Add Vehicle">
        </form>
    </main>
</body>
</html>
