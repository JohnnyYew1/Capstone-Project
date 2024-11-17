<?php
session_start();
include 'db.php';

// Check if UserID is provided
if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);

    // Fetch user details
    $query = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "No users found.";
        exit();
    }
    $stmt->close();

    // Fetch vehicles for the dropdown if user is a driver (UserType = 3)
    $vehicles = [];
    if ($user['UserType'] == 3) {
        $vehicleQuery = "SELECT LicensePlate FROM Vehicle WHERE Status = 1"; // Only active vehicles
        $vehicleResult = $conn->query($vehicleQuery);
        if ($vehicleResult->num_rows > 0) {
            while ($row = $vehicleResult->fetch_assoc()) {
                $vehicles[] = $row['LicensePlate'];
            }
        }
    }
} else {
    echo "No UserID provided.";
    exit();
}

// Handle form submission for user update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['FullName'];
    $phoneNumber = $_POST['PhoneNumber'];
    $assignedLicense = ($user['UserType'] == 3 && isset($_POST['LicensePlate'])) ? $_POST['LicensePlate'] : null;

    // Update user information and license plate if applicable
    $query = "UPDATE Users SET FullName = ?, PhoneNumber = ?, LicensePlate = ? WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $phoneNumber, $assignedLicense, $userID);
    $stmt->execute();
    $stmt->close();

    // Redirect after update
    $_SESSION['message'] = "User Updated Sucessfully";
    $_SESSION['error'] = false;
    header("Location: user_management.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_user.css">
</head>
<body>

    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Update User</h1>
        <form action="" method="POST">
            <label for="FullName">Full Name:</label>
            <input type="text" id="FullName" name="FullName" value="<?php echo htmlspecialchars($user['FullName']); ?>" required>

            <label for="PhoneNumber">Phone Number (60+):</label>
            <input type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" required>

            <?php if ($user['UserType'] == 3): ?>
                <label for="LicensePlate">Assign Vehicle (License Plate):</label>
                <select id="LicensePlate" name="LicensePlate" required>
                    <option value="">Select a License Plate</option>
                    <?php foreach ($vehicles as $licensePlate): ?>
                        <option value="<?php echo $licensePlate; ?>" 
                            <?php echo ($user['LicensePlate'] === $licensePlate) ? 'selected' : ''; ?>>
                            <?php echo $licensePlate; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <input type="submit" value="Update User">
        </form>
    </main>

</body>
</html>
