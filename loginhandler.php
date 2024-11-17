<?php
session_start();
include 'db.php';
$email = $_POST['email'];
$entered_password = $_POST['loginPasskey'];

$stmt = $conn->prepare("SELECT Passkey, UserType, UserID, FullName, Status FROM users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($stored_password, $user_type, $user_id, $full_name, $status);
    $stmt->fetch();

    if (password_verify($entered_password, $stored_password) && $status == 1) {
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['fullName'] = $full_name;

        switch ($user_type) {
            case 1: // Student
                header("Location: home.php");
                exit();
            case 2: // Admin
                header("Location: admin_panel.php");
                exit();
            case 3: // Driver
                header("Location: driver_panel.php");
                exit();
            default:
                $_SESSION['message'] = "Invalid user type.";
                $_SESSION['error'] = true;
                header("Location: login.php");
                exit();
        }
    } else {
        $_SESSION['message'] = "Invalid email or password or account.";
        $_SESSION['error'] = true;
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid email or password.";
    $_SESSION['error'] = true;
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>
