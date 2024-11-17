<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

$vehicles = [];
$query = "SELECT LicensePlate FROM Vehicle WHERE LicensePlate NOT IN (SELECT LicensePlate FROM Route)";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row['LicensePlate'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startLocation = $_POST['StartLocation'];
    $endLocation = $_POST['EndLocation'];
    $distance = $_POST['Distance'];
    $pickupPoints = $_POST['PickUpPoints'];
    $licensePlate = $_POST['LicensePlate'];

    // Check if StartLocation and EndLocation are the same
    if ($startLocation == $endLocation) {
        $_SESSION['message'] = "Start and End Location cannot be the Same";
        $_SESSION['error'] = true;
        header("Location: add_route.php");
        exit();
    } else {
        // Check for duplicate StartLocation and EndLocation combination
        $checkQuery = "SELECT 1 FROM Route WHERE StartLocation = ? AND EndLocation = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $startLocation, $endLocation);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // If a duplicate is found
            $_SESSION['message'] = "Route already Exists";
            $_SESSION['error'] = true;
            header("Location: add_route.php");
            exit();
        } else {
            // Insert the new route if no duplicate or invalid locations
            $query = "INSERT INTO Route (StartLocation, EndLocation, Distance, LicensePlate) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssds", $startLocation, $endLocation, $distance, $licensePlate);

            if ($stmt->execute()) {
                $routeID = $conn->insert_id;

                // Insert pickup points
                $queryPickUp = "INSERT INTO PickUpPoint (RouteID, PickUpLocation) VALUES (?, ?)";
                $stmtPickUp = $conn->prepare($queryPickUp);

                foreach ($pickupPoints as $point) {
                    $stmtPickUp->bind_param("is", $routeID, $point);
                    $stmtPickUp->execute();
                }

                $stmtPickUp->close();

                $_SESSION['message'] = "Route Added Successfully";
                $_SESSION['error'] = false;
                header("Location: route_management.php");
                exit();
            } else {
                $_SESSION['message'] = "Cannot Add Route" . $conn->error;
                $_SESSION['error'] = true;
                header("Location: add_route.php");
                exit();
            }

            $stmt->close();
        }
        $checkStmt->close();
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_route.css">
    <script>
        function addPickUpPoint() {
            const pickupPointList = document.getElementById('pickupPointList');
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = 'PickUpPoints[]';
            newInput.placeholder = 'Enter Pick Up Point';
            newInput.required = true;
            pickupPointList.appendChild(newInput);
        }
    </script>
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Add Route</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message'], $_SESSION['error']);?>
            </div>
        <?php endif; ?>
        <form action="add_route.php" method="POST">
            <label for="StartLocation">Start Location:</label>
            <input type="text" id="StartLocation" name="StartLocation" required>

            <label for="EndLocation">End Location:</label>
            <input type="text" id="EndLocation" name="EndLocation" required>

            <label for="Distance">Distance (km):</label>
            <input type="number" step="0.1" id="Distance" name="Distance" min="2.00" required>

            <label for="LicensePlate">Assign Vehicle:</label>
            <select id="LicensePlate" name="LicensePlate" required>
                <option value="">Select a vehicle</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo htmlspecialchars($vehicle); ?>">
                        <?php echo htmlspecialchars($vehicle); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label>Pickup Points:</label>
            <div id="pickupPointList">
                <input type="text" name="PickUpPoints[]" placeholder="Enter Pick Up Point" required>
            </div>
            <input type="submit" onclick="addPickUpPoint()" value="Add More Pickup Points">
            <br><br>
            <input type="submit" value="Add Route">
        </form>
    </main>
</body>
</html>
