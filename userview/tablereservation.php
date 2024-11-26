<?php
session_name("user_session");
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
    <div class="menu_nav text-center mb-3">
    <button class="menu-nav" id="indoorButton">Indoor</button>
    <button class="menu-nav" id="outdoorButton">Outdoor</button>
</div>

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
                <button type="submit" class="btn proceed-button" onclick="order_list()">Back</button>
            <button class="btn proceed-button ml-2" id="return" onclick="savedreservation()">Proceed</button>
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
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="date" class="form-control" id="reservationDate" name="reservation_date" required onchange="loadAvailableTimes()">
            </div>
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

                // Get the current date and time
                const currentDate = new Date();
                const currentDateString = currentDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                const currentTimeString = currentDate.toTimeString().split(' ')[0].slice(0, 5); // Get time in HH:MM format

                // If the selected date is in the past, remove all times
                if (date < currentDateString) {
                    timeSelect.innerHTML = '<option value="">Selected date is in the past</option>';
                    return; // Exit the function if the date is in the past
                }

                // Filter available times
                const availableTimes = allTimes.filter(time => {
                    // If the selected date is today, exclude past times
                    if (date === currentDateString) {
                        if (time < currentTimeString) {
                            return false; // Exclude past times
                        }
                    }
                    return !unavailableSet.has(time); // Exclude unavailable times
                });

                // Display available times
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
                Swal.fire({
                    icon: 'success',
                    title: 'Reservation Confirmed',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    showConfirmButton: true
                });
            }
            $('#reservationFormModal').modal('hide');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error processing your reservation.',
                showConfirmButton: true
            });
        });
    });

    function showAlertModal(message, type = 'success') {
        let iconType = 'success';
        if (type === 'error') {
            iconType = 'error';
        } else if (type === 'info') {
            iconType = 'info';
        } else {
            iconType = 'warning';
        }

        Swal.fire({
            icon: iconType,
            title: 'Notification',
            text: message,
            showConfirmButton: false,
            timer: 3000
        });
    }

// filter
if (typeof selectedArea === 'undefined') {
    let selectedArea = null; // Declare only if not already defined
}

document.addEventListener('DOMContentLoaded', function() {
    showAllTables(); // Default: Show all tables
});

// Add event listeners to buttons
document.getElementById('indoorButton').addEventListener('click', function() {
    filterTables('Indoor', this);
});

document.getElementById('outdoorButton').addEventListener('click', function() {
    filterTables('Outdoor', this);
});

document.getElementById('showAllButton').addEventListener('click', function() {
    filterTables(null, this); // Show all tables
});

// Function to toggle active class and filter tables based on selected area
function filterTables(area, button) {
    const tables = document.querySelectorAll('.table-item');
    const noTablesMessage = document.getElementById('noTablesMessage');
    const buttons = document.querySelectorAll('.menu-nav');
    
    // Toggle the active state of the button
    if (button.classList.contains('active')) {
        button.classList.remove('active');
        selectedArea = null; // Reset the filter to show all tables
    } else {
        buttons.forEach(btn => btn.classList.remove('active')); // Remove active from all buttons
        button.classList.add('active'); // Add active to the clicked button
        selectedArea = area; // Set selected area
    }

    let hasVisibleTables = false;

    // Show or hide tables based on selected area (Indoor/Outdoor)
    tables.forEach(table => {
        const tableArea = table.getAttribute('data-area');
        if (selectedArea === null || tableArea === selectedArea) {
            table.style.display = 'block';
            hasVisibleTables = true; // At least one table is visible
        } else {
            table.style.display = 'none';
        }
    });

    // Show or hide the "No Tables Available" message
    if (hasVisibleTables) {
        noTablesMessage.style.display = 'none';
    } else {
        noTablesMessage.style.display = 'block';
    }
}


$(document).ready(function() {
    // AJAX call to disabletable.php to check if orders and reservations exist
    $.ajax({
        url: '/Usercontrol/disabletable.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
// Disable the Payment button if no reservations exist
if (!response.has_reservations) {
                    $('#return').attr('disabled', true).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.5'
                    }).on('click', function(e) {
                        e.preventDefault();
                        alert("Please reserve a table before proceeding to payment.");
                    });
                }// Disable the Payment button if no reservations exist
                if (!response.has_reservations) {
                    $('#proceedpayment').attr('disabled', true).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.5'
                    }).on('click', function(e) {
                        e.preventDefault();
                        alert("Please reserve a table before proceeding to payment.");
                    });
                }

                // Disable the Payment button if no reservations exist
                if (!response.has_reservations) {
                    $('#proceedpayment').attr('disabled', true).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.5'
                    }).on('click', function(e) {
                        e.preventDefault();
                        alert("Please reserve a table before proceeding to payment.");
                    });
                }
            } else {
                console.error("Server error:", response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:", textStatus, errorThrown);
        }
    });
});

</script>

<style>
/* Styling for the table selection container */
.table-selection-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Uniform width */
    gap: 20px;
    justify-content: center;
    padding: 20px;
    background-color: #D9D9D9; /* Light background for the table selection area */
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">