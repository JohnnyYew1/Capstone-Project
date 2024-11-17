<?php

include 'db.php';

/**
 * Fetches the email address of a user from the database based on their UserID.
 *
 * @param int $UserID The ID of the user.
 * @param mysqli $conn The database connection.
 * @return string|null The user's email address, or null if not found.
 */
function getUserEmail($UserID, $conn) {
    $query = "SELECT Email FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
    return $email;
}

/**
 * Sends a booking confirmation email to the user.
 *
 * @param string $email The email address to send the confirmation to.
 * @param int $UserID The ID of the user who made the booking.
 * @param int $routeID The ID of the booked route.
 * @param int $seatNumber The booked seat number.
 * @param string $bookingDate The date of the booking.
 * @param string $pickupTime The pickup time for the booking.
 * @param string $licensePlate The license plate of the vehicle.
 * @param string $departureTime The departure time of the shuttle.
 * @param string $tripDate The trip date for the shuttle.
 * @return void
 */
function sendConfirmationEmail($email, $seatNumber, $bookingDate, $pickupTime, $licensePlate, $departureTime, $tripDate) {
    // Prepare the email details
    $subject = "Shuttle Booking Confirmation";
    $message = "
    Dear User,

    Thank you for booking with us! Here are your booking details:

    - License Plate:    $licensePlate
    - Seat Number:      $seatNumber
    - Booking Date:     $bookingDate
    - Pickup Time:      $pickupTime
    - Departure Time:   $departureTime
    - Trip Date:        $tripDate

    IMPORTANT: CANCELLATION IS NOT ALLOWED 1 HOUR BEFORE PICKUP OR DEPARTURE TIME

    If you have any questions, feel free to contact us.

    Best regards,
    Shuttle Booking Team
    ";

    // Headers for the email
    $headers = "From: no-reply@shuttlebooking.com\r\n";
    $headers .= "Reply-To: support@shuttlebooking.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send the email and check for success
    if (!mail($email, $subject, $message, $headers)) {
        error_log("Failed to send email to: " . $email);
        return false; // Return false if sending fails
    }
    
    return true; // Return true if sent successfully
}
?>
