<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $vehicleId = $_GET['id'];

    $query = "SELECT * FROM vehicle WHERE LicensePlate = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vehicleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();

    if (!$vehicle) {
        echo "Vehicle not found.";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newLicensePlate = $_POST['LicensePlate'];
        $vehicleType = $_POST['VehicleType'];
        $capacity = $_POST['Capacity'];

        $updateQuery = "UPDATE Vehicle SET LicensePlate = ?, VehicleType = ?, Capacity = ? WHERE LicensePlate = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssis", $newLicensePlate, $vehicleType, $capacity, $vehicleId);

        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Successfully Updated";
            $_SESSION['error'] = false;
            header("Location: vehicle_management.php");
            exit();
        } else {
            $_SESSION['message'] = "Failed to Update";
            $_SESSION['error'] = true;
            header("Location: vehicle_management.php");
        }
    }
} else {
    $_SESSION['message'] = "No Vehicle ID Provided";
    $_SESSION['error'] = true;
    header("Location: vehicle_management.php");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Vehicle</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_user.css">
</head>
<body>

    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Update Vehicle</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="LicensePlate">License Plate:</label>
            <input type="text" id="LicensePlate" name="LicensePlate" value="<?php echo htmlspecialchars($vehicle['LicensePlate']); ?>" required>

            <label for="VehicleType">Vehicle Type:</label>
            <input type="text" id="VehicleType" name="VehicleType" value="<?php echo htmlspecialchars($vehicle['VehicleType']); ?>" required>

            <label for="Capacity">Capacity:</label>
            <select id="Capacity" name="Capacity" required>
                <option value="13" <?php echo ($vehicle['Capacity'] == 13) ? 'selected' : ''; ?>>13</option>
                <option value="16" <?php echo ($vehicle['Capacity'] == 16) ? 'selected' : ''; ?>>16</option>
            </select>

            <input type="submit" value="Update Vehicle">
        </form>
    </main>

</body>
</html>