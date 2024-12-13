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

// Fetch reservations
$query = "
    SELECT reservation_id, table_id, reservation_date, reservation_time, status, custom_note 
    FROM data_reservations
    WHERE user_id = ?
    ORDER BY reservation_date DESC, reservation_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!-- Progress Bar -->
<style>
    .progress-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .progress-step {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ddd;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 10px;
        font-weight: bold;
        color: #fff;
    }

    .progress-step.active {
        background-color: #007bff;
    }

    .progress-line {
        width: 50px;
        height: 4px;
        background-color: #ddd;
        align-self: center;
    }
    .progress-line.active {
        background-color: #007bff; /* Change to blue */
    }
    .btn:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }
</style>

<div class="progress-container">
    <div class="progress-step active"onclick="ordertable()">1</div>
    <div class="progress-line active"></div>
    <div class="progress-step active"onclick="order_list()">2</div>
    <div class="progress-line active"></div>
    <div class="progress-step active"onclick="submitReservationForm()" >3</div>
    <div class="progress-line active"></div>
    <div class="progress-step active" >4</div>
    <div class="progress-line "></div>
    <div class="progress-step ">5</div>
</div>

<?php

echo '<div class="container my-4">';
echo '<h4 class="text-center">Your Reservations</h4>';
echo '    <div class="row p-3" style="background-color: #D9D9D9; border-radius: 10px;  justify-content: space-evenly">';

if ($result->num_rows > 0) {
    while ($reservation = $result->fetch_assoc()) {
        $reservationId = htmlspecialchars($reservation['reservation_id']);
        $reservationDate = htmlspecialchars($reservation['reservation_date']);
        $reservationTime24 = htmlspecialchars($reservation['reservation_time']);
        $customNote = htmlspecialchars($reservation['custom_note']);
        $status = htmlspecialchars($reservation['status']);

        // Convert 24-hour format to 12-hour AM/PM format
        $reservationTime12 = '';
        if ($reservationTime24) {
            $time = DateTime::createFromFormat('H:i:s', $reservationTime24);
            $reservationTime12 = $time ? $time->format('g:i A') : $reservationTime24;
        }

        // Reservation card with necessary data attributes
        echo '<div class="col-md-6 col-lg-3 mb-4">';
        echo '<div class="reservation-card p-3 border rounded" 
              data-reservation-id="' . $reservationId . '"
              data-table-id="' . htmlspecialchars($reservation['table_id']) . '"
              data-reservation-date="' . $reservationDate . '"
              data-reservation-time="' . $reservationTime24 . '"
              data-custom-note="' . $customNote . '">';
        echo '<h5>Reservation ID: ' . $reservationId . '</h5>';
        echo '<p><strong>Date:</strong> ' . $reservationDate . '</p>';
        echo '<p><strong>Time:</strong> ' . $reservationTime12 . '</p>';
        echo '<p><strong>Status:</strong> ' . $status . '</p>';
        echo '<p><strong>Note:</strong> ' . $customNote . '</p>';
        echo '<button class="btn btn-primary edit-btn" onclick="openEditReservation(this)">Edit</button>';
        echo '<button class="btn btn-danger delete-btn ml-2" onclick="openDeleteConfirmation(' . $reservationId . ')">Delete</button>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12 text-center mt-4" style="padding: 20px; background-color: #f8f9fa; border-radius: 10px;">';
    echo '<i class="fas fa-calendar-times" style="font-size: 3em; color: #ccc; margin-bottom: 10px;"></i>';
    echo '<p style="font-size: 1.2em; color: #555;">No reservations found.</p>';
    echo '<p style="color: #777;">You can make a reservation by clicking the button below.</p>';
    echo '<button class="btn btn-primary" onclick="reservetable()">Make a Reservation</button>';
    echo '</div>';
}

echo '</div>'; // End row
echo '</div>'; // End container

$stmt->close();
$conn->close();
?>

  <div class="containers mt-4">
        <div class="d-flex justify-content-end">
        <button id="" class="btn proceed-button" onclick="reservetable()">Back</button>
            <button id="CARDbutton" class="btn proceed-button ml-2" onclick="paymentlist()" style="width:fit-content;" >Proceed-Payment</button>
        </div>
    </div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center" id="notificationMessage">
                <!-- Success or error message with icon will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Reservation Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1" role="dialog" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editReservationForm" action="/Usercontrol/updatereservation.php" method="POST">
                    <input type="hidden" id="editReservationId" name="reservation_id">
                    <input type="hidden" id="editTableId" name="table_id">

                    <input type="hidden" id="originalReservationDate" name="original_reservation_date">
                    <input type="hidden" id="originalReservationTime" name="original_reservation_time">
                    <input type="hidden" id="originalCustomNote" name="original_custom_note">

                    <div class="form-group">
                        <label for="editReservationDate">Date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" class="form-control" id="editReservationDate" name="reservation_date" onchange="loadAvailableTimesForEdit()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editReservationTime">Time</label>
                        <select class="form-control" id="editReservationTime" name="reservation_time" required>
                            <option value="">Select a time</option>
                            <!-- Time options will be dynamically populated here -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editCustomNote">Custom Note</label>
                        <textarea class="form-control" id="editCustomNote" name="custom_note"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Reservation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this reservation?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteReservation()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

function openDeleteConfirmation(reservationId) {
    Swal.fire({
        title: 'Confirm Deletion',
        text: "Are you sure you want to delete this reservation?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmDeleteReservation(reservationId);
        }
    });
}

function confirmDeleteReservation(reservationId) {
    if (!reservationId) {
        console.error('Error: Reservation ID is missing or invalid');
        showNotificationModal('Reservation ID is missing or invalid.', 'error');
        return;
    }

    console.log('Attempting to delete reservation with ID:', reservationId);

    fetch('/Usercontrol/deleteReservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reservation_id: reservationId })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);

        if (data.status === 'success') {
            showNotificationModal(data.message, 'success');

            const reservationCard = document.querySelector(`.reservation-card[data-reservation-id="${reservationId}"]`);
            if (reservationCard) {
                reservationCard.remove();
            }
        } else {
            showNotificationModal(data.message || 'Failed to delete reservation.', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotificationModal('An unexpected error occurred while attempting to delete the reservation.', 'error');
    });
}

function showNotificationModal(message, type) {
    let iconType = type === 'success' ? 'success' : 'error';
    Swal.fire({
        icon: iconType,
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

// Function to open the Edit Reservation Modal
function openEditReservation(button) {
    const reservationCard = button.closest('.reservation-card');

    if (!reservationCard) {
        console.error('Error: Unable to find reservation card.');
        return;
    }

    // Populate form fields with data attributes from the card
    document.getElementById('editReservationId').value = reservationCard.getAttribute('data-reservation-id');
    document.getElementById('editTableId').value = reservationCard.getAttribute('data-table-id');
    document.getElementById('editReservationDate').value = reservationCard.getAttribute('data-reservation-date');
    document.getElementById('editCustomNote').value = reservationCard.getAttribute('data-custom-note');

    // Retrieve and set the time
    const originalTime = reservationCard.getAttribute('data-reservation-time');
    if (originalTime) {
        document.getElementById('editReservationTime').value = originalTime;
        document.getElementById('originalReservationTime').value = originalTime;
    } else {
        document.getElementById('editReservationTime').value = ''; // Clear if no time is set
    }

    // Load available times and highlight the current reservation time if necessary
    loadAvailableTimesForEdit(originalTime);

    // Show the modal
    $('#editReservationModal').modal('show');
}

// Event listener for submitting the edit form
document.getElementById('editReservationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);
    const reservationId = document.getElementById('editReservationId').value;

    // Get current form values
    const newDate = document.getElementById('editReservationDate').value;
    const newTime = document.getElementById('editReservationTime').value;
    const newNote = document.getElementById('editCustomNote').value.trim();

    // Get original values from hidden fields
    const originalDate = document.getElementById('originalReservationDate').value;
    const originalTime = document.getElementById('originalReservationTime').value;
    const originalNote = document.getElementById('originalCustomNote').value.trim();

    // Enhanced change detection logic
    const hasChanges = newDate !== originalDate || newTime !== originalTime || newNote !== originalNote;

    if (hasChanges) {
        fetch('/Usercontrol/updatereservation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotificationModal('Reservation updated successfully.', 'success');
                $('#editReservationModal').modal('hide'); // Close the modal on success

                // Update the reservation card without reloading
                const reservationCard = document.querySelector(`.reservation-card[data-reservation-id="${reservationId}"]`);
                if (reservationCard) {
                    // Update card details
                    reservationCard.querySelector('p:nth-child(2)').innerHTML = `<strong>Date:</strong> ${newDate}`;
                    reservationCard.querySelector('p:nth-child(3)').innerHTML = `<strong>Time:</strong> ${newTime}`;
                    reservationCard.querySelector('p:nth-child(5)').innerHTML = `<strong>Note:</strong> ${newNote}`;
                }
            } else {
                const message = data.message || 'Failed to update reservation. Please try again.';
                showNotificationModal(message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotificationModal('An error occurred while updating the reservation. Please check your network and try again.', 'error');
        });
    } else {
        showNotificationModal('No changes detected. Please modify at least one field before submitting.', 'info');
    }
});

// Function to load available times for editing
function loadAvailableTimesForEdit(currentTime) {
    const tableId = document.getElementById('editTableId').value;
    const date = document.getElementById('editReservationDate').value;

    if (date) {
        fetch(`/Usercontrol/getAvailableTimes.php?table_id=${tableId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                const timeSelect = document.getElementById('editReservationTime');
                timeSelect.innerHTML = '<option value="">Select a time</option>'; // Clear previous options

                const allTimes = [];
                const currentDate = new Date(); // Get current date and time
                const selectedDate = new Date(date); // Parse the selected date

                for (let hour = 7; hour <= 18; hour++) {
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        const hourString = hour < 10 ? `0${hour}` : `${hour}`;
                        const minuteString = minutes < 10 ? `0${minutes}` : `${minutes}`;
                        const time24 = `${hourString}:${minuteString}`;
                        allTimes.push(time24);
                    }
                }

                const unavailableSet = new Set();
                data.unavailable.forEach(time => unavailableSet.add(time));

                const availableTimes = allTimes.filter(time => {
                    // Check if time is unavailable
                    if (unavailableSet.has(time)) return false;

                    // Check if time is in the past for the current date
                    const [hour, minute] = time.split(':').map(Number);
                    const timeDate = new Date(selectedDate);
                    timeDate.setHours(hour, minute, 0, 0);

                    return selectedDate > currentDate || timeDate > currentDate;
                });

                if (availableTimes.length > 0) {
                    availableTimes.forEach(time24 => {
                        const [hour, minute] = time24.split(':');
                        let hour12 = hour % 12 || 12;
                        const amPm = hour < 12 ? 'AM' : 'PM';
                        const time12 = `${hour12}:${minute} ${amPm}`;

                        const option = document.createElement('option');
                        option.value = time24;
                        option.textContent = time12;

                        // Highlight the current time
                        if (time24 === currentTime) {
                            option.selected = true;
                        }

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

$(document).ready(function() {
    // AJAX call to check if orders and reservations exist
    $.ajax({
        url: '/Usercontrol/disabletable.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Disable the table button if no orders exist
                if (!response.has_orders) {
                    $('#CARDbutton')
                        .attr('disabled', true)
                        .css({
                            'cursor': 'not-allowed',
                            'opacity': '0.5'
                        })
                        .on('click', function(e) {
                            e.preventDefault(); // Prevent default behavior
                            alert("Please add an order before completing the reservation.");
                        });
                }

                // Disable the Payment button if no reservations exist
                if (!response.has_reservations) {
                    $('#CARDbutton, #return').attr('disabled', true).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.5'
                    }).on('click', function(e) {
                        e.preventDefault();
                        alert("Please reserve a table before proceeding to payment.");
                    });
                }
            } else {
                console.error("Failed to fetch table status:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error during AJAX request:", error);
        }
    });
});

</script>


<style>
.reservation-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    width: 100%;
}

.reservation-card {
    background-color: #fff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    min-height: 250px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
}

.reservation-card:hover {
    transform: scale(1.03);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.edit-btn, .delete-btn {
    font-size: 0.8rem;
    margin-top: 10px;
    width: 48%;
}

/* Responsive Styling */
@media (max-width: 992px) {
    .reservation-card {
        flex: 1 1 calc(33% - 15px);
    }
}

@media (max-width: 768px) {
    .reservation-card {
        flex: 1 1 calc(50% - 15px);
    }
}

@media (max-width: 576px) {
    .reservation-card {
        flex: 1 1 100%;
    }
}

</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">