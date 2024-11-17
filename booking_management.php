<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM booking ORDER BY BookingID ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="booking_management.css">
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Vehicle Management</h1>

        <section id="booking-list">
            <h2>Current Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking Date</th>
                        <th>Pickup Time</th>
                        <th>Departure Time</th>
                        <th>Pickup Points</th>
                        <th>User ID</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['BookingDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['PickUp']); ?></td>
                                <td><?php echo htmlspecialchars($row['DepartureTime']); ?></td>
                                <td><?php echo htmlspecialchars($row['PickUpPoint']); ?></td>
                                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                                <td><?php echo htmlspecialchars($row['Status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No Bookings found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>

<?php
mysqli_close($conn);
?>