<?php
session_start();
include 'db.php';

$full_name = $_POST['fullName'];
$email = $_POST['email'];
$phone = $_POST['phoneNumber'];
$password = $_POST['Passkey'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Invalid email format.";
    $_SESSION['error'] = true;
    header("Location: register.php");
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['message'] = "Password must be at least 8 characters long.";
    $_SESSION['error'] = true;
    header("Location: register.php");
    exit();
}

if (strpos($email, '@student.newinti.edu.my') !== false) {
    $user_type = 1;  // Student
} elseif (strpos($email, '@admin.newinti.edu.my') !== false) {
    $user_type = 2;  // Admin
} elseif (strpos($email, '@driver.newinti.edu.my') !== false) {
    $user_type = 3;  // Driver
} else {
    $_SESSION['message'] = "Invalid email domain. Please use a valid student, admin, or driver email.";
    $_SESSION['error'] = true;
    header("Location: register.php");
    exit();
}

$stmt = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['message'] = "Email is already registered. Please use a different email or log in.";
    $_SESSION['error'] = true;
    header("Location: register.php");
    exit();
}

$stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (FullName, Email, PhoneNumber, Passkey, UserType) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $full_name, $email, $phone, $hashed_password, $user_type);

if ($stmt->execute()) {
    $_SESSION['message'] = "Registration successful. You can now log in.";
    $_SESSION['error'] = false;
    header("Location: login.php");
    exit();
} else {
    $_SESSION['message'] = "Error during registration: " . $stmt->error;
    $_SESSION['error'] = true;
    header("Location: register.php");
    exit();
}

$stmt->close();
$conn->close();
?>
