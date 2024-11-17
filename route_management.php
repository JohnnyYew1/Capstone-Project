<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

// Fetch routes along with their assigned vehicles
$query = "
    SELECT r.*, v.LicensePlate 
    FROM route r 
    LEFT JOIN Vehicle v ON r.LicensePlate = v.LicensePlate 
    ORDER BY r.RouteID ASC
";
$routes = mysqli_query($conn, $query);

$pickupQuery = "SELECT * FROM PickUpPoint";
$pickupPoints = mysqli_query($conn, $pickupQuery);
$pickupPointsByRoute = [];

while ($pickupRow = mysqli_fetch_assoc($pickupPoints)) {
    $pickupPointsByRoute[$pickupRow['RouteID']][] = $pickupRow['PickUpLocation'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="route_management.css">
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Route Management</h1>
        <section id="routes-list">
            <?php if (mysqli_num_rows($routes) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Route ID</th>
                            <th>Start Location</th>
                            <th>End Location</th>
                            <th>Distance (km)</th>
                            <th>Vehicle Assigned</th> <!-- New column for Vehicle -->
                            <th>Pick-Up Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($routes)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['RouteID']); ?></td>
                                <td><?php echo htmlspecialchars($row['StartLocation']); ?></td>
                                <td><?php echo htmlspecialchars($row['EndLocation']); ?></td>
                                <td><?php echo htmlspecialchars($row['Distance']); ?></td>
                                <td><?php echo htmlspecialchars($row['LicensePlate'] ?: 'No vehicle assigned'); ?></td> <!-- Display assigned vehicle -->
                                <td>
                                    <ul>
                                        <?php
                                        if (isset($pickupPointsByRoute[$row['RouteID']])) {
                                            foreach ($pickupPointsByRoute[$row['RouteID']] as $pickupPoint) {
                                                echo "<li>" . htmlspecialchars($pickupPoint) . "</li>";
                                            }
                                        } else {
                                            echo "<li>No pickup points</li>";
                                        }
                                        ?>
                                    </ul>
                                </td>
                                <td>
                                    <a href="update_route.php?id=<?php echo $row['RouteID']; ?>" class="activate-link">Update</a>
                                    <a href="route_status.php?id=<?php echo $row['RouteID']; ?>&current_status=<?php echo $row['Status']; ?>"  
                                        class="<?php echo ($row['Status'] == 1) ? 'deactivate-link' : 'activate-link'; ?>">
                                            <?php echo ($row['Status'] == 1) ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <tr><td colspan="7">No routes found</td></tr> <!-- Adjusted colspan to match new column -->
            <?php endif; ?>
        </section>

        <a href="add_route.php" class="float-button">
            <img src="https://png.pngtree.com/png-vector/20190214/ourmid/pngtree-vector-plus-icon-png-image_515260.jpg" alt="Create Route" class="plus-icon">
        </a>
    </main>

</body>
</html>

<?php
mysqli_close($conn);
?>
