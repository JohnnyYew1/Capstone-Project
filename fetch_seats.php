<?php
include 'db.php';

$licensePlate = $_GET['license_plate'];
$bookingDate = $_GET['trip_date'];
$pickupTime = $_GET['pickup_time'];

// Query to get booked seats for the specific license plate, date, and time slot
$queryBookedSeats = "SELECT SeatNumber FROM booking 
                     WHERE LicensePlate = ? 
                       AND TripDate = ? 
                       AND Status = 'Active' 
                       AND PickUp = ?";
$stmtBooked = $conn->prepare($queryBookedSeats);
$stmtBooked->bind_param('sss', $licensePlate, $bookingDate, $pickupTime);
$stmtBooked->execute();
$resultBooked = $stmtBooked->get_result();

$bookedSeats = [];
while ($row = $resultBooked->fetch_assoc()) {
    $bookedSeats[] = $row['SeatNumber'];
}
$stmtBooked->close();

// Query to get all seats for the vehicle
$queryAllSeats = "SELECT SeatNumber FROM seats WHERE LicensePlate = ?";
$stmtSeats = $conn->prepare($queryAllSeats);
$stmtSeats->bind_param('s', $licensePlate);
$stmtSeats->execute();
$resultSeats = $stmtSeats->get_result();

$seatingChart = [];
while ($row = $resultSeats->fetch_assoc()) {
    $seatNumber = $row['SeatNumber'];

    // Determine seat status based on bookings
    if (in_array($seatNumber, $bookedSeats)) {
        $seatingChart[$seatNumber] = 'Booked';
    } else {
        $seatingChart[$seatNumber] = 'Available';
    }
}
$stmtSeats->close();
$conn->close();

// Prepare output with seat availability
echo json_encode([
    'seating_chart' => $seatingChart,
    'total_seats' => count($seatingChart) // Include total seats for layout handling
]);
?>
