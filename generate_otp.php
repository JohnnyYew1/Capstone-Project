<?php
session_start();

include 'db.php';
include 'email_otp.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $query = "SELECT UserID FROM Users WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userID);
    $stmt->fetch();
    $stmt->close();

    if (!$userID) {
        $_SESSION['message'] = "No user found with that email.";
        $_SESSION['error'] = true;
        header("Location: request_otp.php");
        exit();
    }

    $_SESSION['user_id'] = $userID;

    $otp = generateOTP();
    
    if (!saveOTP($userID, $otp, $conn)) {
        $_SESSION['message'] = "Failed to generate OTP. Please try again.";
        $_SESSION['error'] = true;
        header("Location: request_otp.php");
        exit();
    }

    if (sendOTPEmail($email, $otp)) {
        $_SESSION['message'] = "An OTP has been sent to your email.";
        $_SESSION['error'] = false;
        header("Location: enter_otp.php"); 
        exit(); 
    } else {
        $_SESSION['message'] = "Failed to send OTP. Please try again.";
        $_SESSION['error'] = true;
        header("Location: request_otp.php");
        exit();
    }
}
$conn->close();
?>
