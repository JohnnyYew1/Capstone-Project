<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM vehicle ORDER BY LicensePlate ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="user_management.css">
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Vehicle Management</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message'], $_SESSION['error']);?>
            </div>
        <?php endif; ?>
        <section id="vehicle-list">
            <h2>Current Vehicles</h2>
            <table>
                <thead>
                    <tr>
                        <th>License Plate</th>
                        <th>Vehicle Type</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['LicensePlate']); ?></td>
                                <td><?php echo htmlspecialchars($row['VehicleType']); ?></td>
                                <td><?php echo htmlspecialchars($row['Capacity']); ?></td>
                                <td>
                                <a href="update_vehicles.php?id=<?php echo $row['LicensePlate']; ?>" class="activate-link">Edit</a>
                                <a href="vehicles_status.php?id=<?php echo $row['LicensePlate']; ?>&current_status=<?php echo $row['Status']; ?>"  
                                        class="<?php echo ($row['Status'] == 1) ? 'deactivate-link' : 'activate-link'; ?>">
                                            <?php echo ($row['Status'] == 1) ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No vehicles found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <a href="add_vehicles.php" class="float-button">
            <img src="https://png.pngtree.com/png-vector/20190214/ourmid/pngtree-vector-plus-icon-png-image_515260.jpg" alt="Add Vehicle" class="plus-icon">
        </a>
    </main>

</body>
</html>

<?php
mysqli_close($conn);
?>