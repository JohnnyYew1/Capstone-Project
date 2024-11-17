<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User ID is not set. Please try again.";
    $_SESSION['error'] = true;
    header("Location: enter_otp.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['user_id'];
    $inputOtp = $_POST['otp'];

    $query = "SELECT OTP, OTPExpiry FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($storedOtp, $otpExpiry);
    $stmt->fetch();
    $stmt->close();


    if ($storedOtp === $inputOtp && strtotime($otpExpiry) > time()) {
        echo "OTP verified successfully.";
    
        $query = "UPDATE Users SET OTP = NULL, OTPExpiry = NULL WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->close();
 
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid OTP or OTP expired.";
        $_SESSION['error'] = true;
        header("Location: enter_otp.php");
        exit();
    }
}
?>
