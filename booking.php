<?php
session_start();
include 'navigation.php';
include 'db.php';

// Fetch routes and pickup points from the database
$queryRoutes = "SELECT RouteID, StartLocation, EndLocation, LicensePlate FROM Route WHERE Status = 1";
$resultRoutes = $conn->query($queryRoutes);
$routes = [];
$licensePlates = [];
while ($row = $resultRoutes->fetch_assoc()) {
    $routes[$row['RouteID']] = $row['StartLocation'] . ' - ' . $row['EndLocation'];
    $licensePlates[$row['RouteID']] = $row['LicensePlate'];
}

$queryPickups = "SELECT RouteID, PickUpLocation FROM PickUpPoint";
$resultPickups = $conn->query($queryPickups);
$pickupPoints = [];
while ($row = $resultPickups->fetch_assoc()) {
    $pickupPoints[$row['RouteID']][] = $row['PickUpLocation'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Shuttle</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="booking.css">
</head>
<body>
<main>
    <h2>Book Your Shuttle</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="booking-container">
        <form action="bookinghandler.php" method="POST" class="form-box">
            <label for="route">Select Route:</label>
            <select name="route" id="route" required onchange="updatePickupPoints(); updateSeats();">
                <option value="">-- Select a Route --</option>
                <?php foreach ($routes as $id => $name): ?>
                    <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="license_plate" id="license_plate" value="">
            <label for="pickup">Select Pickup Point:</label>
            <select name="pickup" id="pickup" required>
                <option value="">-- Select a Pickup Point --</option>
            </select>

            <label for="trip_date">Trip Date:</label>
            <input type="date" name="trip_date" id="trip_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                max="<?php echo date('Y-m-d', strtotime('+1 week')); ?>">

            <label for="pickup_time">Pickup Time:</label>
            <select name="pickup_time" id="pickup_time" required onchange="updateSeats();">
                <option value="">-- Select Pickup Time --</option>
                <option value="7:00 AM">7:00 AM</option>
                <option value="9:00 AM">9:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option valye="none">--none--</option>
            </select>

            <label for="departure_time">Departure Time:</label>
            <select name="departure_time" id="departure_time" >
                <option value="">-- Select Departure Time --</option>
                <option value="1:10 PM">1:10 PM</option>
                <option value="3:10 PM">3:10 PM</option>
                <option value="5:10 PM">5:10 PM</option>
                <option valye="none">--none--</option>
            </select>

            <label for="seat">Choose Seat:</label>
            <select name="seat" id="seat" required>
                <option value="">-- Select Seat --</option>
            </select>

            <input type="submit" value="Book Shuttle">
        </form>

        <div id="seating-chart" class="seating-chart"></div>
    </div>
</main>

<script>
const pickupPoints = <?= json_encode($pickupPoints) ?>;
const licensePlates = <?= json_encode($licensePlates) ?>;

function updatePickupPoints() {
    const routeSelect = document.getElementById('route');
    const pickupSelect = document.getElementById('pickup');
    const licensePlateInput = document.getElementById('license_plate');
    
    pickupSelect.innerHTML = '<option value="">-- Select a Pickup Point --</option>';
    licensePlateInput.value = '';
    
    const selectedRoute = routeSelect.value;
    if (selectedRoute && pickupPoints[selectedRoute]) {
        pickupPoints[selectedRoute].forEach(point => {
            const option = document.createElement('option');
            option.value = point;
            option.textContent = point;
            pickupSelect.appendChild(option);
        });
        licensePlateInput.value = licensePlates[selectedRoute];
    }
}

function updateSeats() {
    const routeSelect = document.getElementById('route');
    const bookingDateInput = document.getElementById('trip_date');
    const pickupTimeInput = document.getElementById('pickup_time');
    const seatSelect = document.getElementById('seat');

    const licensePlate = licensePlates[routeSelect.value];
    document.getElementById('license_plate').value = licensePlate;

    // Reset dropdown and seating chart
    seatSelect.innerHTML = '<option value="">-- Select Seat --</option>';
    renderSeatingChart({}, 0);

    // Validate if required fields are filled
    if (routeSelect.value && bookingDateInput.value && pickupTimeInput.value) {
        // Fetch seat availability dynamically
        fetch(`fetch_seats.php?license_plate=${licensePlate}&trip_date=${bookingDateInput.value}&pickup_time=${pickupTimeInput.value}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch seat availability');
                }
                return response.json();
            })
            .then(data => {
                // Render seating chart
                renderSeatingChart(data.seating_chart, data.total_seats);

                // Populate available seats in the dropdown
                const availableSeats = Object.keys(data.seating_chart).filter(
                    seat => data.seating_chart[seat] === 'Available'
                );

                seatSelect.innerHTML = '<option value="">-- Select Seat --</option>';
                if (availableSeats.length > 0) {
                    availableSeats.forEach(seat => {
                        const option = document.createElement('option');
                        option.value = seat; // Seat numbers directly
                        option.textContent = `Seat ${seat}`;
                        seatSelect.appendChild(option);
                    });
                } else {
                    $_SESSION['message'] = "Full Booking";
                    $_SESSION['error'] = true;
                    header("Location: booking.php");
                    exit();
                }
            })
            .catch(error => {
                console.error('Error fetching seat availability:', error);
                alert('Error fetching seat availability. Please try again later.');
            });
    }
}



function renderSeatingChart(seatingChart, totalSeats) {
    const seatingDisplay = document.getElementById('seating-chart');
    seatingDisplay.innerHTML = '';

    // Determine rows based on seat count
    let rows = Math.ceil(totalSeats / 3); // Three seats per row layout

    let layoutHTML = '<div class="seating-layout">';

    // Add driver seat in the first row and last column
    layoutHTML += `<div class="seat driver-seat" style="grid-row: 1; grid-column: 3;" id="driver">Driver</div>`;

    // Initialize seat number for subsequent rows
    let seatNum = 1;

    // Loop to add seats dynamically starting from the second row
    for (let row = 2; row <= rows + 1; row++) { // Ensure enough rows are created
        for (let col = 1; col <= 3; col++) {
            if (seatNum <= totalSeats) { // Correct condition to render exactly 13 seats
                layoutHTML += `<div class="seat ${seatingChart[seatNum] === 'Booked' ? 'booked' : 'available'}" 
                                style="grid-row: ${row}; grid-column: ${col};" id="seat-${seatNum}">S${seatNum}</div>`;
                seatNum++;
            }
        }
    }

    layoutHTML += '</div>';
    seatingDisplay.innerHTML = layoutHTML;
}



</script>

</body>
</html>
