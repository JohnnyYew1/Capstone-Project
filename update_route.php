<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $routeID = intval($_GET['id']);

    $query = "SELECT * FROM Route WHERE RouteID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $routeID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $route = $result->fetch_assoc();
    } else {
        echo "No route found.";
        exit();
    }
    $stmt->close();

    $query = "SELECT PickUpLocation FROM PickUpPoint WHERE RouteID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $routeID);
    $stmt->execute();
    $pickupResult = $stmt->get_result();
    $pickupPoints = [];
    while ($row = $pickupResult->fetch_assoc()) {
        $pickupPoints[] = $row['PickUpLocation'];
    }
    $stmt->close();
} else {
    echo "No RouteID provided.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startLocation = $_POST['StartLocation'];
    $endLocation = $_POST['EndLocation'];
    $distance = $_POST['Distance'];
    $newPickupPoints = $_POST['PickUpPoints'];

    $query = "UPDATE Route SET StartLocation = ?, EndLocation = ?, Distance = ? WHERE RouteID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $startLocation, $endLocation, $distance, $routeID);
    $stmt->execute();
    $stmt->close();

    $query = "DELETE FROM PickUpPoint WHERE RouteID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $routeID);
    $stmt->execute();
    $stmt->close();

    $query = "INSERT INTO PickUpPoint (RouteID, PickUpLocation) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    foreach ($newPickupPoints as $point) {
        $stmt->bind_param("is", $routeID, $point);
        $stmt->execute();
    }
    $stmt->close();

    header("Location: route_management.php?success=Route updated successfully");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Route</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_user.css">
</head>
<body>

    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Update Route</h1>
        <form action="" method="POST">
            <label for="StartLocation">Start Location:</label>
            <input type="text" id="StartLocation" name="StartLocation" value="<?php echo htmlspecialchars($route['StartLocation']); ?>" required>

            <label for="EndLocation">End Location:</label>
            <input type="text" id="EndLocation" name="EndLocation" value="<?php echo htmlspecialchars($route['EndLocation']); ?>" required>

            <label for="Distance">Distance (km):</label>
            <input type="number" step="0.1" id="Distance" name="Distance" value="<?php echo htmlspecialchars($route['Distance']); ?>" required>

            <div id="pickup-points">
                <label for="PickUpPoints[]">Pickup Points:</label>
                <?php foreach ($pickupPoints as $point): ?>
                    <input type="text" name="PickUpPoints[]" value="<?php echo htmlspecialchars($point); ?>" required>
                <?php endforeach; ?>
            </div>
            <input type="submit" onclick="addPickupPoint()"value = "Add another pickup point">
            <br><br>
            <input type="submit" value = "Update Route">
        </form>
    </main>

    <script>
        function addPickupPoint() {
            const div = document.createElement('div');
            div.innerHTML = '<input type="text" name="PickUpPoints[]" required>';
            document.getElementById('pickup-points').appendChild(div);
        }
    </script>

</body>
</html>
