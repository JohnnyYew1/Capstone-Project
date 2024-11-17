<?php
session_start();
include 'db.php';
include 'email.php'; // Include the email functionality

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

$UserID = $_SESSION['user_id'];
$route = $_POST['route'];
$pickup = $_POST['pickup'];
$bookingDate = date('Y-m-d H:i:s');
$tripDate = $_POST['trip_date'];
$pickup_time = $_POST['pickup_time'];
$departure_time = $_POST['departure_time'];
$seat = intval($_POST['seat']);

$today = date('Y-m-d');
$oneWeekFromNow = date('Y-m-d', strtotime('+1 week'));

// Check if the trip date is within one week and not on a weekend
if ($tripDate < $today || $tripDate > $oneWeekFromNow || in_array(date('N', strtotime($tripDate)), [6, 7])) {
    $_SESSION['message'] = "You can only book trips up to one week in advance and cannot select weekends.";
    $_SESSION['error'] = true;
    header("Location: booking.php");
    exit();
}

// Check if the user already has an active booking for the given trip date
$checkBookingQuery = "SELECT TripDate FROM Booking WHERE UserID = ? AND Status = 'Active'";
$stmtCheckBooking = $conn->prepare($checkBookingQuery);
$stmtCheckBooking->bind_param('i', $UserID);
$stmtCheckBooking->execute();
$resultBookings = $stmtCheckBooking->get_result();

$activeBookings = [];
while ($row = $resultBookings->fetch_assoc()) {
    $activeBookings[] = $row['TripDate'];
}

if (in_array($tripDate, $activeBookings)) {
    $_SESSION['message'] = "You cannot have more than one active booking for this trip date.";
    $_SESSION['error'] = true;
    header("Location: booking.php");
    exit();
}

// Check for active bookings within the week
$weekStartDate = date('Y-m-d', strtotime($tripDate . ' -6 days'));
$weekEndDate = $tripDate;

$checkWeeklyBookingQuery = "SELECT TripDate FROM Booking WHERE UserID = ? AND Status = 'Active' AND TripDate BETWEEN ? AND ?";
$stmtCheckWeeklyBooking = $conn->prepare($checkWeeklyBookingQuery);
$stmtCheckWeeklyBooking->bind_param('iss', $UserID, $weekStartDate, $weekEndDate);
$stmtCheckWeeklyBooking->execute();
$resultWeeklyBookings = $stmtCheckWeeklyBooking->get_result();

// Fetch the LicensePlate based on the selected route
$queryVehicle = "SELECT LicensePlate FROM Route WHERE RouteID = ?";
$stmtVehicle = $conn->prepare($queryVehicle);
$stmtVehicle->bind_param("i", $route);
$stmtVehicle->execute();
$resultVehicle = $stmtVehicle->get_result();
$vehicle = $resultVehicle->fetch_assoc();

if ($vehicle) {
    $LicensePlate = $vehicle['LicensePlate'];
} else {
    $_SESSION['message'] = "Invalid route selected.";
    $_SESSION['error'] = true;
    header("Location: booking.php");
    exit();
}

// Check if the seat is still available
$checkSeatQuery = "SELECT SeatNumber FROM Booking WHERE LicensePlate = ? AND BookingDate <= ? AND SeatNumber= ?";
$stmtCheckSeat = $conn->prepare($checkSeatQuery);
$stmtCheckSeat->bind_param('ssi', $LicensePlate, $bookingDate, $seat);
$stmtCheckSeat->execute();
$stmtCheckSeat->store_result();

$seatQuery = "SELECT SeatNumber FROM seats WHERE LicensePlate = ? AND isAvailable = 1 AND SeatNumber = ?";
$seatStmt = $conn->prepare($seatQuery);
$seatStmt->bind_param("si", $LicensePlate, $seat);
$seatStmt->execute();
$seatStmt->bind_result($seat);
$seatStmt->fetch();
$seatStmt->close();

$updateSeatQuery = "UPDATE Seats SET isAvailable = 0 WHERE LicensePlate = ? AND SeatNumber = ?";
$stmtUpdateSeat = $conn->prepare($updateSeatQuery);
$stmtUpdateSeat->bind_param("si", $LicensePlate, $seat);
$stmtUpdateSeat->execute();
$stmtUpdateSeat->close();

// Insert booking into the database
$queryInsertBooking = "INSERT INTO Booking (UserID, RouteID, PickUpPoint, BookingDate, TripDate, Pickup, 
                                            DepartureTime, SeatNumber, LicensePlate, Status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";

$stmtInsertBooking = $conn->prepare($queryInsertBooking);
$stmtInsertBooking->bind_param('iisssssis', $UserID, $route, $pickup, $bookingDate, $tripDate, $pickup_time, 
                                            $departure_time, $seat, $LicensePlate);

if ($stmtInsertBooking->execute()) {
    // Get user's email address
    $userEmail = getUserEmail($UserID, $conn);

    // Send confirmation email
    if ($userEmail && sendConfirmationEmail($userEmail, $seat, $bookingDate, $pickup_time, $LicensePlate, $departure_time, $tripDate)) {
        $_SESSION['message'] = "Shuttle booked successfully! A confirmation email has been sent.";
        $_SESSION['error'] = false;
    } else {
        $_SESSION['message'] = "Booking successful but failed to send confirmation email.";
        $_SESSION['error'] = true;
    }
} else {
    $_SESSION['message'] = "There was an error booking the shuttle. Please try again.";
    $_SESSION['error'] = true;
}

$stmtInsertBooking->close();
$conn->close();

header("Location: booking.php");
exit();
?>
