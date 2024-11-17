<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $is_scheduled = isset($_POST['is_scheduled']);
    $status = isset($_POST['status']) ? 1 : 0;
    $createdBy = $_SESSION['user_id'];
    $user_type = $_POST['user_type'];

    $schedule_date = $is_scheduled ? mysqli_real_escape_string($conn, $_POST['publish_date']) : NULL;
    $schedule_time = $is_scheduled ? mysqli_real_escape_string($conn, $_POST['publish_time']) : NULL;
    $scheduled_datetime = ($schedule_date && $schedule_time) ? "$schedule_date $schedule_time" : NULL;

    $sql = "INSERT INTO Announcements (Title, Message, ScheduledDate, ScheduledDateTime, Status, CreatedBy, UserType) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $title, $message, $schedule_date, $scheduled_datetime, $status, $createdBy, $user_type);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Announcement created successfully.";
    } else {
        $_SESSION['error'] = "Error: Could not create the announcement.";
    }
    
    $stmt->close();
    $conn->close();

    header("Location: announcements_admin.php");
    exit();
}
?>
