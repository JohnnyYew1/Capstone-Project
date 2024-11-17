<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $routeID = intval($_GET['id']);

    $query = "SELECT Status FROM Route WHERE RouteID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $routeID);
        $stmt->execute();
        $stmt->bind_result($currentStatus);
        $stmt->fetch();
        $stmt->close();

        $newStatus = ($currentStatus == 1) ? 0 : 1;

        $query = "UPDATE route SET Status = ? WHERE RouteID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $newStatus, $routeID);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Status updated successfully";
                $_SESSION['error'] = false;
                header("Location: route_management.php");
                exit();
            } else {
                echo "Error updating status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No route ID provided.";
}

$conn->close();
?>