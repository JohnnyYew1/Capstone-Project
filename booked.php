<?php
session_start();

include 'navigation.php';
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 1) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT DISTINCT b.BookingID, b.DepartureTime, b.TripDate, b.Pickup, b.PickUpPoint, r.EndLocation, 
           v.LicensePlate, v.VehicleType, s.SeatNumber, r.StartLocation
    FROM Booking b
    JOIN Route r ON b.RouteID = r.RouteID
    JOIN Vehicle v ON b.LicensePlate = v.LicensePlate
    JOIN Seats s ON b.SeatNumber = s.SeatNumber
    WHERE b.UserID = ? AND b.Status = 'active' AND b.TripDate > CURDATE()";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    echo "Error in query: " . $conn->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Bookings</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="announcement.css">
    <link rel="stylesheet" href="booked.css">
</head>
<body>
    <main>
        <h1>Your Active Bookings</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($bookings)): ?>
            <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Trip Date</th>
                        <th>Pick up Time</th>
                        <th>Departure Time</th>
                        <th>Start Location</th>
                        <th>End Location</th>
                        <th>Vehicle</th>
                        <th>Seat Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['BookingID']); ?></td>
                            <td><?php echo htmlspecialchars($booking['TripDate']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Pickup']); ?></td>
                            <td><?php echo htmlspecialchars($booking['DepartureTime']); ?></td>
                            <td><?php echo htmlspecialchars($booking['StartLocation']. ' (' . $booking['PickUpPoint'] . ')'); ?></td>
                            <td><?php echo htmlspecialchars($booking['EndLocation']); ?></td>
                            <td><?php echo htmlspecialchars($booking['LicensePlate']); ?></td>
                            <td><?php echo htmlspecialchars($booking['SeatNumber']); ?></td>
                            <td>
                                <?php
                                $pickupDateTime = strtotime($booking['TripDate'] . ' ' . $booking['DepartureTime']);
                                $canCancel = ($pickupDateTime - time()) > 3600;
                                if ($canCancel): ?>
                                    <a href="cancel_booking.php?id=<?php echo $booking['BookingID']; ?>" class="delete-link" onclick="return confirm('Are you sure?')">Cancel</a>
                                <?php else: ?>
                                    <span>Cannot cancel (Less than 1 hour)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </di>
        <?php else: ?>
            <p>No active bookings found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
