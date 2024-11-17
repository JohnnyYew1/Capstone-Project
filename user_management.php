<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

$queryUsers = "SELECT * FROM users WHERE UserType = 1 ORDER BY UserID ASC";
$queryDrivers = "SELECT * FROM users WHERE UserType = 3 ORDER BY UserID ASC";

$users = mysqli_query($conn, $queryUsers);
$drivers = mysqli_query($conn, $queryDrivers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="user_management.css">
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
    <h1>User Management</h1>
    <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message'], $_SESSION['error']);?>
            </div>
        <?php endif; ?>
    <div class="tabs">
        <div class="tab <?php echo !isset($_GET['type']) || $_GET['type'] == 1 ? 'active' : ''; ?>">
            <a href="?type=1">Users</a>
        </div>
        <div class="tab <?php echo isset($_GET['type']) && $_GET['type'] == 3 ? 'active' : ''; ?>">
            <a href="?type=3">Drivers</a>
        </div>
    </div>

    <section id="users-list">
        <?php
        $selectedType = isset($_GET['type']) ? intval($_GET['type']) : 1;
        $usersToDisplay = ($selectedType == 1) ? $users : $drivers;

        if (mysqli_num_rows($usersToDisplay) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <?php if ($selectedType == 3): ?>
                            <th>License Plate</th>
                        <?php endif; ?>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($usersToDisplay)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                            <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                            <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                            <?php if ($selectedType == 3): ?>
                                <td><?php echo htmlspecialchars($row['LicensePlate']); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($row['UserType']); ?></td>
                            <td>
                                <a href="update_users.php?id=<?php echo $row['UserID']; ?>" class="activate-link">Update</a>
                                <a href="users_status.php?id=<?php echo $row['UserID']; ?>&current_status=<?php echo $row['Status']; ?>"  
                                    class="<?php echo ($row['Status'] == 1) ? 'deactivate-link' : 'activate-link'; ?>">
                                        <?php echo ($row['Status'] == 1) ? 'Deactivate' : 'Activate'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </section>

    <a href="add_users.php" class="float-button">
        <img src="https://png.pngtree.com/png-vector/20190214/ourmid/pngtree-vector-plus-icon-png-image_515260.jpg" alt="Add User" class="plus-icon">
    </a>
</main>


</body>
</html>

<?php
mysqli_close($conn);
?>
