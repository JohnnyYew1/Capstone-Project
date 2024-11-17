<?php
session_start();

include 'db.php';
include 'navigation.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 1) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

$query = "SELECT FullName, Email, PhoneNumber FROM Users WHERE UserID = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $_SESSION['message'] = "Error retrieving user data.";
    $_SESSION['error'] = true;
    header('Location: profile.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];

    $updateQuery = "UPDATE Users SET FullName = ?, PhoneNumber = ? WHERE UserID = ?";
    if ($updateStmt = $conn->prepare($updateQuery)) {
        $updateStmt->bind_param('ssi', $fullName, $phone, $userID);
        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully.";
            $_SESSION['error'] = false;
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['message'] = "Error updating profile.";
            $_SESSION['error'] = true;
            header("Location: profile.php");
            exit();
        }
        $updateStmt->close();
    } else {
        $_SESSION['message'] = "Error preparing query: " . $conn->error;
        $_SESSION['error'] = true;
        header("Location: profile.php");
        exit();
    }
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <main>
        <h1>Profile</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="post">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['FullName']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" required>
            <br><br>
            <input type="submit" value="Save Changes">
        </form>
    </main>
</body>
</html>
