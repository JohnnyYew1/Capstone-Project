<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 3) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['fullName'];
?>

<?php
if (isset($_SESSION['message'])) {
    $messageType = $_SESSION['error'] ? 'error' : 'success';
    echo "<div class='message {$messageType}'>{$_SESSION['message']}</div>";
    unset($_SESSION['message'], $_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home - Shuttle Booking</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="LogoInti.png" alt="Shuttle Logo">
        </div>
        <ul>
            <li><a href="schedule.php">Schedule</a></li>
            <li><a href="announcements_driver.php">Announcements</a></li>
            <li><a href="profile_driver.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
    <h2>Welcome, Driver!</h2>
            <section class="hero">
                <div class="welcome">
                    <h1>Welcome, <?php echo $user; ?>!</h1>
                    <p>Plan your day by viewing your schedule and stay updated with the latest announcements.</p>
                </div>
                <div class="hero-image">
                    <img src="shuttle1.jpeg" alt="Shuttle Bus1">
                </div>
            </section>

            <section class="content">
                <div class="booking">
                    <h2>View Your Schedule</h2>
                    <p>Check the amount of students for today's routes and pickup points.</p>
                    <a href="schedule.php" class="cta-button">View Now</a>
                </div>

                <div class="announcements">
                    <h2>Latest Announcements</h2>
                    <p>Stay up-to-date with important updates regarding shuttle services.</p>
                    <a href="announcements_driver.php" class="cta-button">View Announcements</a>
                </div>
            </section>
        </main>
    <footer>
        <p>&copy; 2024 Shuttle Booking System. All rights reserved.</p>
    </footer>
</body>
</html>
