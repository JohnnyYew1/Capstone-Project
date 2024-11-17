<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

include 'navbar_admin.php';
include 'db.php';

$usersData = [];
$searchQuery = '';

// Fetch counts for overview
$userCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(Status = 1) AS active, SUM(Status = 0) AS inactive FROM users WHERE UserType = 1"));
$driverCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(Status = 1) AS active, SUM(Status = 0) AS inactive FROM users WHERE UserType = 3"));
$vehicleCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(Status = 1) AS active, SUM(Status = 0) AS inactive FROM vehicle"));
$routeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(Status = 1) AS active, SUM(Status = 0) AS inactive FROM route"));

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}
$query = "
    SELECT 
        Users.FullName, 
        Users.PhoneNumber,
        SUM(CASE WHEN Booking.Status = 'active' THEN 1 ELSE 0 END) AS ActiveCount,
        SUM(CASE WHEN Booking.Status = 'completed' THEN 1 ELSE 0 END) AS CompletedCount,
        SUM(CASE WHEN Booking.Status = 'cancelled' THEN 1 ELSE 0 END) AS CanceledCount
    FROM Users
    LEFT JOIN Booking ON Users.UserID = Booking.UserID
    WHERE Users.FullName LIKE ? AND Users.UserType = 1 /* Filter by UserType */
    GROUP BY Users.UserID
";
$stmt = $conn->prepare($query);
$searchParam = "%" . $searchQuery . "%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $usersData[] = $row;
}

$stmt->close();
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_panel.css">
</head>
<body>

<main>
    <h1>Welcome, Admin</h1>
    
    <!-- Search and User Display Section -->
    <div class="dashboard">
    <div class="user-booking-box">
        <h2>Users' Booking</h2>
        <form method="get">
            <input type="text" name="search" placeholder="Search user by name" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">🔍</button>
        </form>

        <div id="user-info">
            <p><strong>Name:</strong> <span id="user-name"></span></p>
            <p><strong>Phone:</strong> <span id="user-phone"></span></p>
        </div>

        <div class="booking-counts">
            <div id="active-count" class="count-box">
                <span></span> 
                <p>Active</p>
            </div>
            <div id="completed-count" class="count-box">
                <span></span> 
                <p>Completed</p>
            </div>
            <div id="canceled-count" class="count-box">
                <span></span> 
                <p>Canceled</p>
            </div>
        </div>


        <div class="arrows">
            <button onclick="prevUser()">◀️</button>
            <button onclick="nextUser()">▶️</button>
        </div>
    </div>

    <!-- Overview Section -->
    <div class="overview">
        <h2>Dashboard Overview</h2>
        <div class="overview-box">
            <div class="overview-item">
                <h3>Users</h3>
                <p><span class="status-indicator active"></span> Active: <?php echo $userCount['active']; ?></p>
                <p><span class="status-indicator inactive"></span> Inactive: <?php echo $userCount['inactive']; ?></p>
                <p>Total: <?php echo $userCount['total']; ?></p>
            </div>
            <div class="overview-item">
                <h3>Drivers</h3>
                <p><span class="status-indicator active"></span> Active: <?php echo $driverCount['active']; ?></p>
                <p><span class="status-indicator inactive"></span> Inactive: <?php echo $driverCount['inactive']; ?></p>
                <p>Total: <?php echo $driverCount['total']; ?></p>
            </div>
            <div class="overview-item">
                <h3>Vehicles</h3>
                <p><span class="status-indicator active"></span> Active: <?php echo $vehicleCount['active']; ?></p>
                <p><span class="status-indicator inactive"></span> Inactive: <?php echo $vehicleCount['inactive']; ?></p>
                <p>Total: <?php echo $vehicleCount['total']; ?></p>
            </div>
            <div class="overview-item">
                <h3>Routes</h3>
                <p><span class="status-indicator active"></span> Active: <?php echo $routeCount['active']; ?></p>
                <p><span class="status-indicator inactive"></span> Inactive: <?php echo $routeCount['inactive']; ?></p>
                <p>Total: <?php echo $routeCount['total']; ?></p>
            </div>
        </div>
    </div>   
    </div>

    <!-- JavaScript for User Navigation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const usersData = <?php echo json_encode($usersData); ?>;
            let currentIndex = 0;

            function displayUser(index) {
                if (usersData.length === 0) {
                    document.getElementById("user-name").innerText = "No users found";
                    document.getElementById("user-phone").innerText = "";
                    document.getElementById("active-count").querySelector("span").innerText = "0";
                    document.getElementById("completed-count").querySelector("span").innerText = "0";
                    document.getElementById("canceled-count").querySelector("span").innerText = "0";
                    return;
                }

                const user = usersData[index];
                document.getElementById("user-name").innerText = user.FullName;
                document.getElementById("user-phone").innerText = user.PhoneNumber;
                document.getElementById("active-count").querySelector("span").innerText = user.ActiveCount;
                document.getElementById("completed-count").querySelector("span").innerText = user.CompletedCount;
                document.getElementById("canceled-count").querySelector("span").innerText = user.CanceledCount;
            }

            function nextUser() {
                if (currentIndex < usersData.length - 1) {
                    currentIndex++;
                    displayUser(currentIndex);
                }
            }

            function prevUser() {
                if (currentIndex > 0) {
                    currentIndex--;
                    displayUser(currentIndex);
                }
            }

            window.nextUser = nextUser;
            window.prevUser = prevUser;

            // Initial display of the first user
            displayUser(currentIndex);
        });
    </script>
</main>
</body>
</html>
