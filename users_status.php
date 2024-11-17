<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);

    $query = "SELECT Status FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->bind_result($currentStatus);
        $stmt->fetch();
        $stmt->close();

        $newStatus = ($currentStatus == 1) ? 0 : 1;

        $query = "UPDATE Users SET Status = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $newStatus, $userID);
            if ($stmt->execute()) {
                $_SESSION['message'] = "User Status updated successfully";
                $_SESSION['error'] = false;
                header("Location: user_management.php");
                exit();
            } else {
                $_SESSION['message'] = "Error updating status " . $stmt->error;
                $_SESSION['error'] = true;
                header("Location: user_management.php");
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Error connecting to database" . $conn->error;
            $_SESSION['error'] = true;
            header("Location: user_management.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Error during registration: " . $conn->error;
        $_SESSION['error'] = true;
        header("Location: user_management.php");
        exit();
    }
} else {
    $_SESSION['message'] = "No UserID Provided";
    $_SESSION['error'] = true;
    header("Location: user_management.php");
    exit();
}

$conn->close();
?>