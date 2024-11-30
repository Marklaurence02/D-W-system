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
    WHERE user_id = ? AND status = 'Complete'  -- Changed 'Rescheduled' to 'Complete'
    ORDER BY reservation_date DESC, reservation_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="container my-4" style="overflow-x: auto;">';
echo '<h4 class="text-center">Your Complete Reservations</h4>';
echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 justify-content-center">';  // Use row-cols for responsive columns

if ($result->num_rows > 0) {
    while ($reservation = $result->fetch_assoc()) {
        $reservationId = htmlspecialchars($reservation['reservation_id']);
        $reservationDate = htmlspecialchars($reservation['reservation_date']);
        $reservationTime24 = htmlspecialchars($reservation['reservation_time']);
        $customNote = htmlspecialchars($reservation['custom_note']);
        $status = htmlspecialchars($reservation['status']);

        // Check if feedback already exists for this reservation
        $feedbackQuery = "SELECT feedback_id FROM feedbacks WHERE reservation_id = ?";
        $feedbackStmt = $conn->prepare($feedbackQuery);
        $feedbackStmt->bind_param("i", $reservationId);
        $feedbackStmt->execute();
        $feedbackResult = $feedbackStmt->get_result();
        $feedbackExists = $feedbackResult->num_rows > 0;

        $reservationTime12 = '';
        if ($reservationTime24) {
            $time = DateTime::createFromFormat('H:i:s', $reservationTime24);
            $reservationTime12 = $time ? $time->format('g:i A') : $reservationTime24;
        }

        echo '<div class="col d-flex justify-content-center">';  // Use col for responsive column
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

        // Disable button if feedback already exists
        if ($feedbackExists) {
            echo '<button class="btn btn-warning w-100" disabled>Feedback Provided</button>';
        } else {
            echo '<button class="btn btn-warning w-100" onclick="openFeedbackForm(' . $reservationId . ')">Provide Feedback</button>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12 text-center">';
    echo '<p class="text-muted">No Complete reservations found.</p>';
    echo '</div>';
}

echo '</div>'; // End row
echo '</div>'; // End container

$stmt->close();
$conn->close();
?>



<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Provide Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm">
                    <input type="hidden" id="reservationId" name="reservationId">
                    <div class="mb-3">
                        <label for="feedbackText" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedbackText" name="feedbackText" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <div class="star-rating" id="starRating">
                            <span class="star" data-value="1" onclick="selectStar(1)">&star;</span>
                            <span class="star" data-value="2" onclick="selectStar(2)">&star;</span>
                            <span class="star" data-value="3" onclick="selectStar(3)">&star;</span>
                            <span class="star" data-value="4" onclick="selectStar(4)">&star;</span>
                            <span class="star" data-value="5" onclick="selectStar(5)">&star;</span>
                        </div>
                        <div id="ratingText" class="mt-2">Select a rating</div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitFeedback()">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedRating = 0;

function openFeedbackForm(reservationId) {
    document.getElementById('reservationId').value = reservationId;
    $('#feedbackModal').modal('show');  // Use jQuery to show the modal
}

function selectStar(value) {
    selectedRating = value;
    updateStarRating();
    updateRatingText();
}

function updateStarRating() {
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        const starValue = parseInt(star.getAttribute('data-value'));
        if (starValue <= selectedRating) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
}

function updateRatingText() {
    const ratingText = document.getElementById('ratingText');
    switch (selectedRating) {
        case 1:
            ratingText.textContent = "Poor";
            break;
        case 2:
            ratingText.textContent = "Fair";
            break;
        case 3:
            ratingText.textContent = "Good";
            break;
        case 4:
            ratingText.textContent = "Very Good";
            break;
        case 5:
            ratingText.textContent = "Excellent";
            break;
        default:
            ratingText.textContent = "Select a rating";
    }
}

function submitFeedback() {
    const reservationId = document.getElementById('reservationId').value;
    const feedbackText = document.getElementById('feedbackText').value;
    
    if (!feedbackText || !selectedRating) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Fields',
            text: 'Please fill in all fields.'
        });
        return;
    }

    fetch('/Usercontrol/submitFeedback.php', {
        method: 'POST',
        body: new URLSearchParams({
            reservationId: reservationId,
            feedbackText: feedbackText,
            rating: selectedRating
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Feedback Submitted',
                text: data.message
            }).then(() => {
                Feedback();
                $('#feedbackModal').modal('hide');
                $('.modal-backdrop').remove(); 
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error("Error submitting feedback:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to submit feedback. Please try again.'
        });
    });
}
</script>

<style>
.star {
    font-size: 1.5rem; 
    color: #ccc;
    cursor: pointer;
    transition: color 0.3s ease;
}

.star.selected {
    color: gold;
}

.star:hover {
    color: orange;
}

#ratingText {
    font-size: 1rem; 
    color: #555;
}

/* Add this media query for mobile devices */
@media (max-width: 768px) {
    .container {
        overflow-x: auto;  /* Enable horizontal scrolling on smaller screens */
    }
}
</style>
