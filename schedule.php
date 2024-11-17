<?php
session_start();
include 'db.php';
include 'navbar_driver.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] != 3) {
    header("Location: login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];

// Fetch the schedule information
// Fetch the schedule information
$query = "
    SELECT DISTINCT b.BookingID, b.TripDate, b.Pickup AS PickupTime, b.PickUpPoint, r.StartLocation, 
           r.EndLocation, v.LicensePlate, v.VehicleType, COUNT(b.SeatNumber) AS StudentCount
    FROM Booking b
    JOIN Route r ON b.RouteID = r.RouteID
    JOIN Vehicle v ON b.LicensePlate = v.LicensePlate
    JOIN Users u ON v.LicensePlate = u.LicensePlate
    WHERE u.UserType = 3 
      AND u.UserID = ? 
      AND b.Status = 'active' 
      AND b.TripDate >= CURDATE()
    GROUP BY b.TripDate, b.Pickup, b.PickUpPoint, r.StartLocation, r.EndLocation, v.LicensePlate";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $driver_id); // Bind the driver ID from session
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $_SESSION['message'] = "Error loading schedule. Please try again later.";
    $_SESSION['error'] = true;
    header("Location: driver_panel.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Schedule</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="announcement.css">
</head>
<body>
    <main>
        <h1>Your Schedule</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($schedule)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Trip Date</th>
                        <th>Pickup Time</th>
                        <th>Start Location</th>
                        <th>Pickup Point</th>
                        <th>End Location</th>
                        <th>Vehicle</th>
                        <th>Student Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $trip): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trip['TripDate']); ?></td>
                            <td><?php echo htmlspecialchars($trip['PickupTime']); ?></td>
                            <td><?php echo htmlspecialchars($trip['StartLocation']); ?></td>
                            <td><?php echo htmlspecialchars($trip['PickUpPoint']); ?></td>
                            <td><?php echo htmlspecialchars($trip['EndLocation']); ?></td>
                            <td><?php echo htmlspecialchars($trip['LicensePlate']); ?></td>
                            <td><?php echo htmlspecialchars($trip['StudentCount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No scheduled trips found.</p>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
