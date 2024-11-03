<?php
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
    echo '<p class="col-12 text-center mt-4">No reservations found.</p>';
}

echo '</div>'; // End row
echo '</div>'; // End container

$stmt->close();
$conn->close();
?>

<!-- button-->
<div class="container mt-4">
    <div class="d-flex justify-content-end">
        <button class="btn proceed-button" onclick="reservetable()">Back</button>
        <form action="User-panel.php" method="post" class="ml-2">
            <button type="submit" class="btn proceed-button" >Complete</button>
        </form>
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
                        <input type="date" class="form-control" id="editReservationDate" name="reservation_date" onchange="loadAvailableTimesForEdit()">
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

<script>

function openDeleteConfirmation(reservationId) {
    document.getElementById('confirmDeleteModal').dataset.reservationId = reservationId;
    $('#confirmDeleteModal').modal('show');
}

function confirmDeleteReservation() {
    const reservationId = document.getElementById('confirmDeleteModal').dataset.reservationId;

    if (!reservationId) {
        console.error('Error: Reservation ID is missing or invalid'); // Debugging log
        showNotificationModal('Reservation ID is missing or invalid.', 'error');
        return;
    }

    console.log('Attempting to delete reservation with ID:', reservationId); // Debugging log

    $('#confirmDeleteModal').modal('hide'); // Hide the delete confirmation modal

    fetch('/Usercontrol/deleteReservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reservation_id: reservationId }) // Ensure reservation_id is sent correctly
    })
    .then(response => {
        console.log('HTTP status:', response.status); // Log HTTP status for debugging
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data); // Log the entire server response

        if (data.status === 'success') {
            showNotificationModal(data.message, 'success');

            // Remove the reservation card without reloading
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
                for (let hour = 7; hour <= 18; hour++) {
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        const hourString = hour < 10 ? `0${hour}` : `${hour}`;
                        const minuteString = minutes < 10 ? `0${minutes}` : `${minutes}`;
                        const time24 = `${hourString}:${minuteString}`;
                        allTimes.push(time24);
                    }
                }

                const unavailableSet = new Set();
                data.unavailable.forEach(time => {
                    unavailableSet.add(time);
                    const [hour, minute] = time.split(':').map(Number);
                    let currentTimeIncrement = new Date(0, 0, 0, hour, minute);

                    for (let i = 0; i < 60; i++) {
                        currentTimeIncrement.setMinutes(currentTimeIncrement.getMinutes() + 1);
                        const currentHour = currentTimeIncrement.getHours().toString().padStart(2, '0');
                        const currentMinute = currentTimeIncrement.getMinutes().toString().padStart(2, '0');
                        unavailableSet.add(`${currentHour}:${currentMinute}`);
                    }
                });

                const availableTimes = allTimes.filter(time => !unavailableSet.has(time));

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

// Function to show the notification modal
function showNotificationModal(message, type) {
    const notificationMessage = document.getElementById('notificationMessage');
    const modalBody = document.querySelector('#notificationModal .modal-body');

    // Clear any existing content and classes
    notificationMessage.innerHTML = '';
    modalBody.classList.remove('bg-success', 'bg-danger');

    // Set icon and background based on message type
    let iconHtml = '';
    if (type === 'success' || type === 'info') {
        modalBody.classList.add('bg-success');
        iconHtml = '<i class="fa fa-check-circle-o" style="font-size: 24px; margin-right: 8px;" aria-hidden="true"></i>';
    } else if (type === 'error') {
        modalBody.classList.add('bg-danger');
        iconHtml = '<i class="fas fa-exclamation-circle" style="font-size: 24px; margin-right: 8px;"></i>';
    }

    // Inject icon and message
    notificationMessage.innerHTML = `${iconHtml}<span>${message}</span>`;

    // Show the modal
    $('#notificationModal').modal('show');

    // Auto-hide the modal after 3 seconds
    setTimeout(() => $('#notificationModal').modal('hide'), 3000);
}



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