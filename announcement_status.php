<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['announcement_id'])) {
    $announcementId = intval($_GET['announcement_id']);

    $query = "SELECT Status FROM announcements WHERE AnnouncementID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();
        $stmt->bind_result($currentStatus);
        $stmt->fetch();
        $stmt->close();

        $newStatus = ($currentStatus == 1) ? 0 : 1;

        $query = "UPDATE announcements SET Status = ? WHERE AnnouncementID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $newStatus, $announcementId);
            if ($stmt->execute()) {
                header("Location: announcements_admin.php?success=Status updated successfully");
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
    echo "No announcement ID provided.";
}

$conn->close();
?>