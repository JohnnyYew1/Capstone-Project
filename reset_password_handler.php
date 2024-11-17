<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $userID = $_SESSION['user_id'];

    if (strlen($newPassword) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        $_SESSION['error'] = true;
        header("Location: reset_password.php");
        exit();
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = "Password does not match.";
        $_SESSION['error'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = "UPDATE Users SET Passkey = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $hashedPassword, $userID);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Password reset successfully.";
            $_SESSION['error'] = false;
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['message'] = "Password reset failed.";
            $_SESSION['error'] = true;
            header("Location: reset_password.php");
            exit();
        }
        $stmt->close();
    }
}
?>