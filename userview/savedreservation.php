<?php
session_start();
include_once "../assets/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT reservation_id, table_id, reservation_date, reservation_time, status, custom_note 
    FROM data_reservations
    WHERE user_id = ?
    ORDER BY reservation_date DESC, reservation_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="container p-4 d-flex flex-column align-items-center text-center reservation-container">';
echo '<h4>Your Reservations</h4>';

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

        echo '<div class="reservation-card my-3 p-3 border rounded w-100" style="max-width: 500px;" data-reservation-id="' . $reservationId . '">';
        echo '<h5>Reservation ID: ' . $reservationId . '</h5>';
        echo '<p><strong>Date:</strong> ' . $reservationDate . '</p>';
        echo '<p><strong>Time:</strong> ' . $reservationTime12 . '</p>';
        echo '<p><strong>Status:</strong> ' . $status . '</p>';
        echo '<p><strong>Note:</strong> ' . $customNote . '</p>';
        echo '<button class="btn btn-primary edit-btn mr-2" onclick="openEditReservation(this)">Edit</button>';
        echo '<button class="btn btn-danger delete-btn" onclick="openDeleteConfirmation(' . $reservationId . ')">Delete</button>';
        echo '</div>';
    }
} else {
    echo '<p class="mt-4">No reservations found.</p>';
}

echo '</div>';

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
        <input type="date" class="form-control" id="editReservationDate" name="reservation_date">
    </div>
    <div class="form-group">
        <label for="editReservationTime">Time</label>
        <input type="time" class="form-control" id="editReservationTime" name="reservation_time">
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
function openEditReservation(button) {
    const reservationCard = button.closest('.reservation-card');
    
    const dataMapping = {
        'data-reservation-id': 'editReservationId',
        'data-table-id': 'editTableId',
        'data-reservation-date': 'editReservationDate',
        'data-reservation-time': 'editReservationTime',
        'data-custom-note': 'editCustomNote'
    };

    for (const [dataAttr, fieldId] of Object.entries(dataMapping)) {
        const value = reservationCard.getAttribute(dataAttr);
        if (value !== null) {
            document.getElementById(fieldId).value = value;
        }
    }
    
    // Set both visible and hidden time fields
    const reservationTime = reservationCard.getAttribute('data-reservation-time');
    if (reservationTime) {
        document.getElementById('editReservationTime').value = reservationTime;
        document.getElementById('originalReservationTime').value = reservationTime;
    }

    $('#editReservationModal').modal('show');
}

document.getElementById('editReservationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('/Usercontrol/updatereservation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotificationModal('Reservation updated successfully.', 'success');
            $('#editReservationModal').modal('hide'); // Close the modal on success
        } else if (data.status === 'info') {
            showNotificationModal('No changes were made.', 'info');
            $('#editReservationModal').modal('hide'); // Close the modal on "No changes"
        } else {
            showNotificationModal('Failed to update reservation: ' + (data.message || 'Unknown error.'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationModal('An error occurred while updating the reservation.', 'error');
    });
});

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
        iconHtml = '<i class="fa fa-check-circle-o"  style="font-size: 24px; margin-right: 8px; aria-hidden="true"></i>';
    } else if (type === 'error') {
        modalBody.classList.add('bg-danger');
        iconHtml = '<i class="fas fa-exclamation-circle" style="font-size: 24px; margin-right: 8px;"></i>';
    }

    // Inject icon and message
    notificationMessage.innerHTML = `${iconHtml}<span>${message}</span>`;

    // Show the notification modal
    $('#notificationModal').modal('show');

    // Auto-hide the modal after 3 seconds
    setTimeout(() => $('#notificationModal').modal('hide'), 3000);
}

</script>


<style>
    /* Reservation Container Styling */
.reservation-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    background-color: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Reservation Card Styling */
.reservation-card {
    width: 100%;
    max-width: 300px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.reservation-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.reservation-card h5 {
    font-size: 1.2rem;
    color: #007bff;
    margin-bottom: 10px;
}

.reservation-card p {
    font-size: 0.95rem;
    color: #495057;
    margin: 5px 0;
}

/* Button Styling */
.edit-btn, .delete-btn {
    font-size: 0.85rem;
    padding: 8px 15px;
    border-radius: 5px;
    margin-top: 10px;
    margin-right: 5px;
    width: 45%;
    transition: background-color 0.3s ease;
}

.edit-btn {
    background-color: #007bff;
    border: none;
    color: #ffffff;
}

.edit-btn:hover {
    background-color: #0056b3;
}

.delete-btn {
    background-color: #dc3545;
    border: none;
    color: #ffffff;
}

.delete-btn:hover {
    background-color: #c82333;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .reservation-card {
        width: 100%;
    }
    .edit-btn, .delete-btn {
        width: 100%;
    }
}

</style>