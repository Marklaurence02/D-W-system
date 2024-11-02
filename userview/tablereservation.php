<?php
session_start();
include_once "../assets/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * Function to get all images for a specific table.
 */
function getTableImages($conn, $tableId) {
    $query = "SELECT image_path FROM table_images WHERE table_id = ? ORDER BY uploaded_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tableId);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
    return $images;
}

?>
<div class="container p-5"> 
    <h4 class="text-center">Reserve a Table</h4>
    <h1 class="text-center">Choose Your Preferred Table!</h1>

    <!-- Table Selection Section -->
    <div class="table-selection-container text-center row" id="tableSelectionContainer">
        <p id="noTablesMessage" class="w-100 text-center mt-4" style="display: none;">No Tables Available</p>

        <?php
        // Fetch all tables
        $query = "SELECT table_id, table_number, seating_capacity, is_available, area FROM tables";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $isAvailable = $row['is_available'] ? 'Available' : 'Not Available';
                $images = getTableImages($conn, $row['table_id']);
                $disabledClass = !$row['is_available'] ? 'disabled' : '';
                $disabledAttr = !$row['is_available'] ? 'disabled' : '';
                ?>
                <div class="table-box col-md-3 table-item" data-availability="<?php echo $row['is_available']; ?>" data-area="<?php echo htmlspecialchars($row['area']); ?>">
                    <button class="table-btn <?php echo $disabledClass; ?>" data-toggle="modal" data-target="#tableModal<?php echo $row['table_id']; ?>" <?php echo $disabledAttr; ?>>
                        <img src="<?php echo htmlspecialchars($images[0] ?? '/path/to/default/image.jpg'); ?>" alt="Table <?php echo htmlspecialchars($row['table_number']); ?>" class="img-fluid rounded">
                    </button>
                    <h6 class="mt-2">Table <?php echo htmlspecialchars($row['table_number']); ?> - <?php echo htmlspecialchars($row['area']); ?></h6>
                    <p>Capacity: <?php echo htmlspecialchars($row['seating_capacity']); ?> | <?php echo $isAvailable; ?></p>
                </div>

                <!-- Modal for Table Details with Carousel -->
                <div class="modal fade" id="tableModal<?php echo $row['table_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="tableModalLabel<?php echo $row['table_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tableModalLabel<?php echo $row['table_id']; ?>">Table <?php echo htmlspecialchars($row['table_number']); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="carouselTable<?php echo $row['table_id']; ?>" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Table Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- Carousel Controls -->
                                    <a class="carousel-control-prev" href="#carouselTable<?php echo $row['table_id']; ?>" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselTable<?php echo $row['table_id']; ?>" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                                <div class="mt-3">
                                    <p><strong>Seating Capacity:</strong> <?php echo htmlspecialchars($row['seating_capacity']); ?></p>
                                    <p><strong>Area:</strong> <?php echo htmlspecialchars($row['area']); ?></p>
                                    <p><strong>Status:</strong> <?php echo $isAvailable; ?></p>
                                    <button class="btn btn-primary" onclick="openReservationForm(<?php echo $row['table_id']; ?>)" <?php echo $disabledAttr; ?>>Reserve Table</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center'>No tables available for reservation</p>";
        }
        ?>
    </div>

    <div class="containers mt-4">
        <div class="d-flex justify-content-end">
            <form action="User-panel.php" method="post">
                <button type="submit" class="btn proceed-button">Home</button>
            </form>
            <button class="btn proceed-button ml-2" onclick="savedreservation()">Proceed</button>
        </div>
    </div>
</div>


<!-- Reservation Form Modal -->
<div class="modal fade" id="reservationFormModal" tabindex="-1" role="dialog" aria-labelledby="reservationFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationFormModalLabel">Reserve Table</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <form id="reservationForm">
        <input type="hidden" id="tableId" name="table_id">
        <div class="form-group">
            <label for="reservationDate">Date</label>
            <input type="date" class="form-control" id="reservationDate" name="reservation_date" required onchange="loadAvailableTimes()">
        </div>
        <div class="form-group">
            <label for="reservationTime">Time</label>
            <select class="form-control" id="reservationTime" name="reservation_time" required>
                <option value="">Select a time</option>
                <!-- Time options will be dynamically populated here -->
            </select>
        </div>
        <div class="form-group">
            <label for="customNote">Custom Note</label>
            <textarea class="form-control" id="customNote" name="custom_note" placeholder="Special requests or notes"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Confirm Reservation</button>
    </form>
</div>
        </div>
    </div>
</div>


<!-- Alert Modal -->
<div class="modal fade" id="tableAlertModal" tabindex="-1" role="dialog" aria-labelledby="tableAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tableAlertModalLabel">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="tableAlertModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadAvailableTimes() {
    const tableId = document.getElementById('tableId').value;
    const date = document.getElementById('reservationDate').value;

    if (date) {
        fetch(`/Usercontrol/getAvailableTimes.php?table_id=${tableId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                const timeSelect = document.getElementById('reservationTime');
                timeSelect.innerHTML = '<option value="">Select a time</option>'; // Clear previous options

                // Generate all times from 7:00 AM to 6:00 PM at 15-minute intervals
                const allTimes = [];
                for (let hour = 7; hour <= 18; hour++) {
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        const hourString = hour < 10 ? `0${hour}` : `${hour}`;
                        const minuteString = minutes < 10 ? `0${minutes}` : `${minutes}`;
                        const time24 = `${hourString}:${minuteString}`;
                        allTimes.push(time24);
                    }
                }

                // Create a set of unavailable times including the 1-hour window
                const unavailableSet = new Set();
                data.unavailable.forEach(time => {
                    // Add the unavailable time itself
                    unavailableSet.add(time);

                    // Add the 1-hour window (at 1-minute increments) after the unavailable time
                    const [hour, minute] = time.split(':').map(Number);
                    let currentTime = new Date(0, 0, 0, hour, minute);

                    for (let i = 0; i < 60; i++) {
                        currentTime.setMinutes(currentTime.getMinutes() + 1);
                        const currentHour = currentTime.getHours().toString().padStart(2, '0');
                        const currentMinute = currentTime.getMinutes().toString().padStart(2, '0');
                        unavailableSet.add(`${currentHour}:${currentMinute}`);
                    }
                });

                // Filter available times
                const availableTimes = allTimes.filter(time => !unavailableSet.has(time));

                if (availableTimes.length > 0) {
                    availableTimes.forEach(time24 => {
                        const [hour, minute] = time24.split(':');
                        let hour12 = hour % 12 || 12; // Convert 24-hour time to 12-hour format
                        const amPm = hour < 12 ? 'AM' : 'PM';
                        const time12 = `${hour12}:${minute} ${amPm}`;

                        const option = document.createElement('option');
                        option.value = time24;
                        option.textContent = time12;
                        timeSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'No available times';
                    option.disabled = true;
                    timeSelect.appendChild(option);
                }
            })
            .catch(() => alert('Error fetching available times. Please try again.'));
    }
}
function openReservationForm(tableId) {
        $('#tableModal' + tableId).modal('hide');
        document.getElementById('tableId').value = tableId;
        $('#reservationFormModal').modal('show');
    } 

    document.getElementById('reservationForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('/Usercontrol/reserveTable.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlertModal(data.message, 'success');
            } else {
                showAlertModal(data.message, 'error');
            }
            $('#reservationFormModal').modal('hide');
        })
        .catch(error => {
            console.error('Error:', error);
            showAlertModal('There was an error processing your reservation.', 'error');
        });
    });

    function showAlertModal(message, type = 'success') {
    const modalMessage = document.getElementById('tableAlertModalMessage');
    const modalBody = document.querySelector('#tableAlertModal .modal-body');

    // Define icon and background color based on the type of alert
    let icon;
    let bgColor;
    
    if (type === 'success') {
        icon = '<i class="fa fa-calendar-plus-o" text-success fa-3x mr-2" aria-hidden="true"></i>';
        bgColor = '#d4edda';  // Light green background for success
    } else if (type === 'error') {
        icon = '<i class="fa fa-calendar-times-o" text-danger fa-3x mr-2" aria-hidden="true"></i>';
        bgColor = '#f8d7da';  // Light red background for errors
    } else if (type === 'info') {
        icon = '<i class="fa fa-info-circle text-info fa-3x mr-2" aria-hidden="true"></i>';
        bgColor = '#d1ecf1';  // Light blue background for info
    } else {
        icon = '<i class="fa fa-exclamation-circle text-warning fa-3x mr-2" aria-hidden="true"></i>';
        bgColor = '#fff3cd';  // Light yellow background for warnings
    }

    // Apply background color to modal body
    modalBody.style.backgroundColor = bgColor;

    // Set message content with icon
    modalMessage.innerHTML = `${icon} <span style="font-size: 1.25rem;">${message}</span>`;

    // Show the modal
    $('#tableAlertModal').modal('show');
}

</script>

<style>
/* Styling for the table selection container */
.table-selection-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Uniform width */
    gap: 20px;
    justify-content: center;
    padding: 20px;
    background-color: #f8f9fa; /* Light background for the table selection area */
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow around the container */
}

/* Individual table card styling */
.table-box {
    width: 100%; /* Full width within the grid */
    max-width: 250px; /* Uniform maximum width */
    margin: 0 auto;
    text-align: center;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Hover effect for table boxes */
.table-box:hover {
    transform: scale(1.05);
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.table-box[data-availability="0"] {
    pointer-events: none;
    opacity: 0.5;
    border: 1px solid #fff;
    box-shadow: 0 0 10px 2px rgba(255, 0, 0, 0.75); /* Red glow effect */
}

.time-slot.active {
    background-color: #007bff;
    color: #fff;
}


/* Responsive adjustments */
@media (max-width: 768px) {
    .table-selection-container {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Adjust for smaller screens */
    }
}


</style>