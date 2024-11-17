<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header('Location: booked.php');
    exit();
}

$bookingID = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Query to retrieve booking details, including SeatNumber
$query = "SELECT PickUp, DepartureTime, Status, TripDate, SeatNumber, LicensePlate FROM Booking WHERE BookingID = ? AND UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $bookingID, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    $_SESSION['message'] = "Booking not found or you do not have permission to cancel this booking.";
    $_SESSION['error'] = true;
    header("Location: booked.php");
    exit();
}

if ($booking['Status'] === 'cancelled') {
    $_SESSION['message'] = "This booking is already canceled.";
    $_SESSION['error'] = true;
    header("Location: booked.php");
    exit();
}

$currentTime = time();
$pickupTime = strtotime($booking['TripDate'] . ' ' . $booking['PickUp']);
$departureTime = strtotime($booking['TripDate'] . ' ' . $booking['DepartureTime']);

if (($pickupTime - $currentTime > 3600) || ($departureTime - $currentTime > 3600)) {
    // Update the booking status to 'Cancelled'
    $updateQuery = "UPDATE Booking SET Status = 'cancelled' WHERE BookingID = ? AND UserID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ii', $bookingID, $user_id);
    $updateStmt->execute();
    $updateStmt->close();

    // Update seat availability
    $seatNumber = $booking['SeatNumber'];
    $licensePlate = $booking['LicensePlate'];
    $updateSeatQuery = "UPDATE Seats SET isAvailable = 1 WHERE SeatNumber = ? AND LicensePlate = ?";
    $updateSeatStmt = $conn->prepare($updateSeatQuery);
    $updateSeatStmt->bind_param('is', $seatNumber, $licensePlate);
    $updateSeatStmt->execute();
    $updateSeatStmt->close();

    $_SESSION['message'] = "Booking canceled successfully and seat availability updated.";
    $_SESSION['error'] = false;
} else {
    $_SESSION['message'] = "You cannot cancel this booking. It is within 1 hour of the scheduled pickup or departure time.";
    $_SESSION['error'] = true;
}

header("Location: booked.php");
exit();
?>
