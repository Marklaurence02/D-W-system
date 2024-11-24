<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT reservation_id, table_id, reservation_date, reservation_time, status, custom_note 
    FROM reservations
    WHERE user_id = ? AND status = 'Rescheduled'
    ORDER BY reservation_date DESC, reservation_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="container my-4">';
echo '<h4 class="text-center">Your Rescheduled Reservations</h4>';
echo '<div class="row g-3 justify-content-center">';  // Added justify-content-center for centering

if ($result->num_rows > 0) {
    while ($reservation = $result->fetch_assoc()) {
        $reservationId = htmlspecialchars($reservation['reservation_id']);
        $reservationDate = htmlspecialchars($reservation['reservation_date']);
        $reservationTime24 = htmlspecialchars($reservation['reservation_time']);
        $customNote = htmlspecialchars($reservation['custom_note']);
        $status = htmlspecialchars($reservation['status']);

        $reservationTime12 = '';
        if ($reservationTime24) {
            $time = DateTime::createFromFormat('H:i:s', $reservationTime24);
            $reservationTime12 = $time ? $time->format('g:i A') : $reservationTime24;
        }

        // Use col-md-6 col-lg-4 for responsive card layout, ensuring they are centered
        echo '<div class="col-md-6 col-lg-4 d-flex justify-content-center">';  // Added d-flex and justify-content-center to center card
        echo '<div class="card border-primary reservation-card" 
                data-reservation-id="' . $reservationId . '" 
                data-table-id="' . $reservation['table_id'] . '" 
                data-reservation-date="' . $reservationDate . '" 
                data-reservation-time="' . $reservationTime24 . '" 
                data-custom-note="' . $customNote . '">';
        echo '<div class="card-header bg-primary text-white">';
        echo "Reservation ID: {$reservationId}";
        echo '</div>';
        echo '<div class="card-body">';
        echo '<p><strong>Date:</strong> ' . $reservationDate . '</p>';
        echo '<p><strong>Time:</strong> ' . $reservationTime12 . '</p>';
        echo '<p><strong>Status:</strong> ' . $status . '</p>';
        echo '<p><strong>Note:</strong> ' . $customNote . '</p>';
        echo '<button class="btn btn-warning w-100" onclick="openEditReservation(' . $reservationId . ')">Edit</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12 text-center">';
    echo '<p class="text-muted">No rescheduled reservations found.</p>';
    echo '</div>';
}

echo '</div>'; // End row
echo '</div>'; // End container

$stmt->close();
$conn->close();
?>



<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="notificationMessage"></span>
            </div>
        </div>
    </div>
</div>


<!-- Edit Reservation Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editReservationForm">
                    <input type="hidden" id="reservationId" name="reservationId">
                    <div class="mb-3">
                        <label for="viewTableId" class="form-label">Table ID</label>
                        <input type="text" class="form-control" id="viewTableId" name="viewTableId" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editDate" class="form-label">Reservation Date</label>
                        <input type="date" class="form-control" id="editDate" name="editDate" required onchange="loadAvailableTimesForEdit()">
                    </div>
                    <div class="mb-3">
                        <label for="editTime" class="form-label">Reservation Time</label>
                        <select class="form-select" id="editTime" name="editTime" required>
                            <option value="">Select a time</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editNote" class="form-label">Note</label>
                        <textarea class="form-control" id="editNote" name="editNote" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="updateReservation()">Update Reservation</button>
                    </form>
            </div>
        </div>
    </div>
</div>


<script>
// JavaScript to open the edit reservation modal and populate fields
function openEditReservation(reservationId) {
    const reservationCard = document.querySelector(`.reservation-card[data-reservation-id="${reservationId}"]`);
    const tableId = reservationCard.dataset.tableId;
    const reservationDate = reservationCard.dataset.reservationDate;
    const reservationTime = reservationCard.dataset.reservationTime;
    const customNote = reservationCard.dataset.customNote;

    // Populate modal fields with reservation data
    document.getElementById('reservationId').value = reservationId;
    document.getElementById('viewTableId').value = tableId;
    document.getElementById('editDate').value = reservationDate;
    document.getElementById('editTime').value = reservationTime;
    document.getElementById('editNote').value = customNote;

    // Show the modal using Bootstrap's Modal API
    const editModal = new bootstrap.Modal(document.getElementById('editReservationModal'));
    editModal.show();

    // Load available times for the selected date
    loadAvailableTimesForEdit(reservationTime);
}

// Function to load available times for editing
function loadAvailableTimesForEdit(currentTime) {
    const tableId = document.getElementById('viewTableId').value;
    const date = document.getElementById('editDate').value;

    if (date) {
        fetch(`/Usercontrol/getAvailableTimes.php?table_id=${tableId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                const timeSelect = document.getElementById('editTime');
                timeSelect.innerHTML = '<option value="">Select a time</option>'; // Clear previous options

                const allTimes = [];
                const currentDate = new Date(); // Get current date and time
                const selectedDate = new Date(date); // Parse the selected date

                // Generate all time slots from 7:00 AM to 6:45 PM in 15-minute intervals
                for (let hour = 7; hour <= 18; hour++) {
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        const hourString = hour < 10 ? `0${hour}` : `${hour}`;
                        const minuteString = minutes < 10 ? `0${minutes}` : `${minutes}`;
                        const time24 = `${hourString}:${minuteString}`;
                        allTimes.push(time24);
                    }
                }

                // Mark unavailable times
                const unavailableSet = new Set();
                data.unavailable.forEach(time => unavailableSet.add(time));

                // Filter out unavailable times and times in the past
                const availableTimes = allTimes.filter(time => {
                    // Check if time is unavailable
                    if (unavailableSet.has(time)) return false;

                    // Check if time is in the past for the selected date
                    const [hour, minute] = time.split(':').map(Number);
                    const timeDate = new Date(selectedDate);
                    timeDate.setHours(hour, minute, 0, 0);

                    return selectedDate > currentDate || timeDate > currentDate;
                });

                // Add available times to the select dropdown
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


function updateReservation() {
    const reservationId = document.getElementById('reservationId').value;
    const editDate = document.getElementById('editDate').value;
    const editTime = document.getElementById('editTime').value;
    const editNote = document.getElementById('editNote').value;

    if (!reservationId || !editDate || !editTime) {
        showNotification("Please fill in all required fields.");
        return;
    }

    // Send the update request to the server
    fetch('Usercontrol/updatereser.php', {
        method: 'POST',
        body: new URLSearchParams({
            reservationId: reservationId,
            editDate: editDate,
            editTime: editTime,
            editNote: editNote
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message);
             // Hide the modal after successful feedback submission
             $('#editReservationModal').modal('hide');
            
            // Remove the modal backdrop
            $('.modal-backdrop').remove(); 
                } else {
            showNotification(data.message);
        }
    })
    .catch(error => {
        console.error("Error updating reservation:", error);
        showNotification("Failed to update reservation. Please try again.");
    });
}



// Show Notification Function
function showNotification(message) {
    const notificationMessage = document.getElementById('notificationMessage');
    notificationMessage.textContent = message;

    const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
    notificationModal.show();
    reschedule()

}



</script>