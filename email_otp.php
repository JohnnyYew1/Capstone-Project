<?php

include 'db.php';

/**
 *
 *
 * @return string The generated OTP.
 */
function generateOTP() {
    return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}


/**
 * Saves the OTP and its expiry time in the database.
 *
 * @param int $userID The ID of the user.
 * @param string $otp The generated OTP.
 * @param mysqli $conn The database connection.
 * @return bool True on success, false on failure.
 */
function saveOTP($userID, $otp, $conn) {
    $expiryTime = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $query = "UPDATE Users SET OTP = ?, OTPExpiry = ? WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $otp, $expiryTime, $userID);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Sends an OTP email to the user.
 *
 * @param string $email The user's email address.
 * @param string $otp The generated OTP.
 * @return bool True if email sent successfully, false otherwise.
 */
function sendOTPEmail($email, $otp) {
    $subject = "Your OTP Code";
    $message = "
    Dear User,

    Your One-Time Password (OTP) is: $otp
    This OTP is valid for the next 10 minutes.

    If you did not request this code, please ignore this email.

    Best regards,
    Shuttle Booking Team
    ";
    
    $headers = "From: no-reply@shuttlebooking.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        error_log("Failed to send OTP to: " . $email);
        return false;
    }
    
    return true;
}

/**
 * Generates and sends an OTP to the user.
 *
 * @param int $userID The user's ID.
 * @param mysqli $conn The database connection.
 * @return bool True if OTP was sent successfully, false otherwise.
 */
function sendUserOTP($userID, $conn) {
    // Fetch user email
    $email = getUserEmail($userID, $conn);
    if (!$email) {
        return false;
    }

    // Generate OTP
    $otp = generateOTP();

    // Save OTP to database
    if (!saveOTP($userID, $otp, $conn)) {
        return false;
    }

    // Send OTP email
    return sendOTPEmail($email, $otp);
}
?>
