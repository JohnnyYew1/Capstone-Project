<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['FullName'];
    $email = $_POST['Email'];
    $phoneNumber = $_POST['PhoneNumber'];
    $passkey = $_POST['Passkey']; 

    if (strpos($email, '@student.newinti.edu.my') !== false) {
        $user_type = 1;
    } elseif (strpos($email, '@admin.newinti.edu.my') !== false) {
        $user_type = 2;
    } elseif (strpos($email, '@driver.newinti.edu.my') !== false) {
        $user_type = 3;
    } else {
        $_SESSION['message'] = "Invalid email domain. Please use a valid student, admin, or driver email.";
        $_SESSION['error'] = true;
        header("Location: add_users.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['error'] = true;
        header("Location: add_users.php");
        exit();
    }
    
    if (strlen($passkey) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        $_SESSION['error'] = true;
        header("Location: add_users.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Email is already registered. Please use a different email or log in.";
        $_SESSION['error'] = true;
        header("Location: add_users.php");
        exit();
    }

    $stmt->close();

    $hashed_password = password_hash($passkey, PASSWORD_DEFAULT);

    $query = "INSERT INTO Users (FullName, Email, PhoneNumber, Passkey, UserType) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $email, $phoneNumber, $hashed_password, $user_type);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User Added Successfully";
        $_SESSION['error'] = false;
        header("Location: add_users.php");
        exit();
    } else {
        $_SESSION['message'] = "Error Adding User";
        $_SESSION['error'] = true;
        header("Location: add_users.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_users.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("Passkey");
            const toggleIcon = document.getElementById("togglePasswordIcon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Add User</h1>
        <form action="add_users.php" method="POST">
            <label for="FullName">Full Name:</label>
            <input type="text" id="FullName" name="FullName" required>

            <label for="Email">Email:</label>
            <input type="email" id="Email" name="Email" required>

            <label for="PhoneNumber">Phone Number:</label>
            <input type="text" id="PhoneNumber" name="PhoneNumber" required>

            <label for="Passkey">Password:</label>
            <div class="password-container">
                <input type="password" id="Passkey" name="Passkey" required>
                <span id="togglePasswordIcon" class="fa fa-eye" onclick="togglePasswordVisibility()"></span>
            </div>

            <input type="submit" value="Add User">
        </form>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message'], $_SESSION['error']);?>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>
