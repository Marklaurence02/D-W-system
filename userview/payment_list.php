<?php
session_start();
include_once "../assets/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch order items for the user
$orderQuery = "
    SELECT oi.order_item_id, oi.order_id, oi.quantity, oi.totalprice, pi.product_name, pi.price
    FROM order_items oi
    JOIN product_items pi ON oi.product_id = pi.product_id
    WHERE oi.user_id = ?
";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orderResult = $stmt->get_result();

$totalPayment = 0;
$orders = [];
while ($row = $orderResult->fetch_assoc()) {
    $orders[] = $row;
    $totalPayment += $row['totalprice'];
}
$stmt->close();

// Fetch reservation details for the user
$reservationQuery = "
    SELECT reservation_id, table_id, reservation_date, reservation_time, status, custom_note
    FROM data_reservations
    WHERE user_id = ?
    ORDER BY reservation_date DESC, reservation_time ASC
";
$stmt = $conn->prepare($reservationQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservationResult = $stmt->get_result();
$reservations = $reservationResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch personal information for the user
$userInfoQuery = "
    SELECT first_name, middle_initial, last_name, suffix, contact_number, email, address, zip_code
    FROM users
    WHERE user_id = ?
";
$stmt = $conn->prepare($userInfoQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userInfoResult = $stmt->get_result();
$userInfo = $userInfoResult->fetch_assoc();
$stmt->close();

// Function to handle data transfer and clearing upon payment
function handleSuccessfulPayment($conn, $user_id, $totalPayment) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert a new record into `receipts`
        $receiptQuery = "
            INSERT INTO receipts (order_id, user_id, total_amount, payment_method)
            SELECT DISTINCT oi.order_id, oi.user_id, ?, 'Credit Card'
            FROM order_items oi
            WHERE oi.user_id = ?
        ";
        $stmt = $conn->prepare($receiptQuery);
        $stmt->bind_param("di", $totalPayment, $user_id);
        $stmt->execute();
        $receipt_id = $conn->insert_id;
        $stmt->close();

        // Insert related order items into `receipt_items`
        $receiptItemsQuery = "
            INSERT INTO receipt_items (receipt_id, product_id, quantity, item_total_price)
            SELECT ?, oi.product_id, oi.quantity, oi.totalprice
            FROM order_items oi
            WHERE oi.user_id = ?
        ";
        $stmt = $conn->prepare($receiptItemsQuery);
        $stmt->bind_param("ii", $receipt_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer data from `data_reservations` to `reservations`
        $transferReservationsQuery = "
            INSERT INTO reservations (reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at)
            SELECT reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at
            FROM data_reservations
            WHERE user_id = ?
        ";
        $stmt = $conn->prepare($transferReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Clear `order_items` and `data_reservations` for the user
        $clearOrderItemsQuery = "DELETE FROM order_items WHERE user_id = ?";
        $stmt = $conn->prepare($clearOrderItemsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $clearReservationsQuery = "DELETE FROM data_reservations WHERE user_id = ?";
        $stmt = $conn->prepare($clearReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();
        echo "Payment and data transfer completed successfully.";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "An error occurred: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'processPayment') {
    handleSuccessfulPayment($conn, $user_id, $totalPayment);
    exit();
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Payment Receipt</h2>

    <!-- Order Items Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Your Orders</h4>
        </div>
        <div class="card-body">
            <?php if (count($orders) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td><?= htmlspecialchars($order['quantity']) ?></td>
                                <td>&#x20B1;<?= number_format($order['price'], 2) ?></td>
                                <td>&#x20B1;<?= number_format($order['totalprice'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No order items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reservation Details Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Your Reservation</h4>
        </div>
        <div class="card-body">
            <?php if (count($reservations) > 0): ?>
                <?php foreach ($reservations as $reservation): ?>
                    <p><strong>Reservation ID:</strong> <?= htmlspecialchars($reservation['reservation_id']) ?></p>
                    <p><strong>Table ID:</strong> <?= htmlspecialchars($reservation['table_id']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($reservation['reservation_date']) ?></p>
                    <p><strong>Time:</strong> <?= date('g:i A', strtotime($reservation['reservation_time'])) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($reservation['status']) ?></p>
                    <p><strong>Custom Note:</strong> <?= htmlspecialchars($reservation['custom_note']) ?></p>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reservation details found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Total Payment Section -->
    <div class="text-right">
        <h4>Total Payment: &#x20B1;<?= number_format($totalPayment, 2) ?></h4>
    </div>
    
    <!-- Buttons for Modals -->
    <div class="text-center mt-4">
        <button class="btn btn-info" data-toggle="modal" data-target="#infoModal">Information</button>
        <button class="btn btn-primary" data-toggle="modal" data-target="#personalInfoModal">Proceed to Payment</button>
    </div>
</div>






<!-- Information Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="infoModalLabel">Additional Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please review your order and reservation details carefully before completing the payment.</p>
                <ul>
                    <li>Make sure the items and quantities are correct.</li>
                    <li>If there are any issues with the reservation, contact our support team.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information Modal -->
<div class="modal fade" id="personalInfoModal" tabindex="-1" role="dialog" aria-labelledby="personalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="personalInfoModalLabel">Confirm Personal Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" class="form-control" id="fullName" value="<?= htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['middle_initial'] . ' ' . $userInfo['last_name'] . ' ' . $userInfo['suffix']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" value="<?= htmlspecialchars($userInfo['contact_number']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($userInfo['email']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" value="<?= htmlspecialchars($userInfo['address']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="zipCode">Zip Code</label>
                        <input type="text" class="form-control" id="zipCode" value="<?= htmlspecialchars($userInfo['zip_code']) ?>" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#paymentModal">Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title text-center w-100" id="receiptModalLabel">Payment Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h4 class="font-weight-bold">Dine&Watch</h4>
                    <div class="receipt-divider"></div>
                </div>
                <h6 class="font-weight-bold">Order Summary</h6>
                <?php if (count($orders) > 0): ?>
                    <ul class="list-unstyled small">
                        <?php foreach ($orders as $order): ?>
                            <li><?= htmlspecialchars($order['product_name']) ?> - Qty: <?= htmlspecialchars($order['quantity']) ?> - Total: &#x20B1;<?= number_format($order['totalprice'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted small">No order details available.</p>
                <?php endif; ?>
                <div class="receipt-divider"></div>
                <h6 class="font-weight-bold">Reservation Summary</h6>
                <?php if (count($reservations) > 0): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <p class="mb-1 small"><strong>ID:</strong> <?= htmlspecialchars($reservation['reservation_id']) ?></p>
                        <p class="mb-1 small"><strong>Table:</strong> <?= htmlspecialchars($reservation['table_id']) ?></p>
                        <p class="mb-1 small"><strong>Date:</strong> <?= htmlspecialchars($reservation['reservation_date']) ?></p>
                        <p class="mb-1 small"><strong>Time:</strong> <?= date('g:i A', strtotime($reservation['reservation_time'])) ?></p>
                        <p class="mb-1 small"><strong>Status:</strong> <?= htmlspecialchars($reservation['status']) ?></p>
                        <p class="mb-1 small"><strong>Note:</strong> <?= htmlspecialchars($reservation['custom_note']) ?></p>
                        <div class="receipt-divider"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">No reservation details available.</p>
                <?php endif; ?>
                <div class="text-right">
                    <h6 class="font-weight-bold">Total Payment: &#x20B1;<?= number_format($totalPayment, 2) ?></h6>
                </div>
            </div>
            <div class="modal-footer">
                <!-- No Return Policy Checkbox -->
                <div class="form-check text-left">
                    <input type="checkbox" class="form-check-input" id="policyCheck" onclick="togglePayButton()" disabled>
                    <label class="form-check-label small" for="policyCheck">
                        I have read and agree to the 
                        <a href="#" data-toggle="modal" data-target="#policyModal" onclick="markPolicyRead()">No Return Policy</a>.
                    </label>
                </div>
                <!-- Pay Button -->
                <button type="button" class="btn btn-success btn-sm" id="payButton" onclick="submitPayment()" disabled>Pay</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Go Back</button>
            </div>
        </div>
    </div>
</div>

<!-- No Return Policy Modal -->
<div class="modal fade" id="policyModal" tabindex="-1" role="dialog" aria-labelledby="policyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="policyModalLabel">No Return Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please carefully read and understand our No Return Policy:</p>
                <ul>
                    <li>All sales are final. No refunds or exchanges.</li>
                    <li>Products and services are sold "as-is" without warranties of any kind.</li>
                    <li>By proceeding with the payment, you agree to accept these terms.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>






<!-- Payment Modal with Card Options -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">Complete Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" onsubmit="return validatePaymentForm()">
                    <!-- Card Image Display on Top -->
                    <div class="text-center mb-3">
                        <img id="selectedCardImage" src="../Images/visa.png" alt="Selected Card" style="width: 100px; height: auto;">
                    </div>

                    <!-- Card Type Selector -->
                    <div class="form-group">
                        <label for="cardType">Select Card Type</label>
                        <select class="form-control" id="cardType" onchange="updateCardDetails()">
                            <option value="" disabled selected>Select a card for testing</option>
                            <option value="visa" data-image="../Images/visa.png" data-card-number="4111 1111 1111 1111" data-cvv="123">Visa - 4111 1111 1111 1111</option>
                            <option value="mastercard" data-image="../Images/mastercard.png" data-card-number="5555 5555 5555 4444" data-cvv="456">MasterCard - 5555 5555 5555 4444</option>
                            <option value="amex" data-image="../Images/amex.png" data-card-number="3782 822463 10005" data-cvv="789">American Express - 3782 822463 10005</option>
                            <option value="gcashcard" data-image="../Images/gcashcard.png" data-card-number="6019 1234 5678 9012" data-cvv="012">GCash Card - 6019 1234 5678 9012</option>
                        </select>
                    </div>

                    <!-- Card Information Section -->
                    <div class="form-group">
                        <label for="cardName">Cardholder Name</label>
                        <input type="text" class="form-control" id="cardName" placeholder="Name on card" value="<?= htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" class="form-control" id="cardNumber" placeholder="Card number" required>
                        <small class="form-text text-danger" id="cardNumberError"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" required>
                            <small class="form-text text-danger" id="expiryDateError"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" placeholder="CVV" required>
                            <small class="form-text text-danger" id="cvvError"></small>
                        </div>
                    </div>
                    <!-- Updated button to show receipt modal -->
                    <button type="button" class="btn btn-primary btn-block" onclick="showReceiptModal()">Check Receipt</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function updateCardDetails() {
    const cardTypeSelect = document.getElementById("cardType");
    const selectedOption = cardTypeSelect.options[cardTypeSelect.selectedIndex];
    
    const selectedCardImage = document.getElementById("selectedCardImage");
    const cardNumberInput = document.getElementById("cardNumber");
    const cvvInput = document.getElementById("cvv");

    // Update the card image, card number, and CVV based on selected option's data attributes
    selectedCardImage.src = selectedOption.getAttribute("data-image") || "../Images/visa.png";
    cardNumberInput.value = selectedOption.getAttribute("data-card-number") || "";
    cvvInput.value = selectedOption.getAttribute("data-cvv") || "";
}

function validatePaymentForm() {
    let isValid = true;
    const cardNumber = document.getElementById("cardNumber").value;
    const expiryDate = document.getElementById("expiryDate").value;
    const cvv = document.getElementById("cvv").value;

    // Regular expressions for validation
    const cardNumberRegex = /^\d{4} \d{4} \d{4} \d{4}$/;
    const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
    const cvvRegex = /^\d{3,4}$/;

    // Validate card number
    if (!cardNumberRegex.test(cardNumber)) {
        document.getElementById("cardNumberError").innerText = "Enter a valid 16-digit card number.";
        isValid = false;
    } else {
        document.getElementById("cardNumberError").innerText = "";
    }

    // Validate expiry date
    if (!expiryDateRegex.test(expiryDate)) {
        document.getElementById("expiryDateError").innerText = "Enter a valid expiry date in MM/YY format.";
        isValid = false;
    } else {
        document.getElementById("expiryDateError").innerText = "";
    }

    // Validate CVV
    if (!cvvRegex.test(cvv)) {
        document.getElementById("cvvError").innerText = "Enter a valid 3 or 4-digit CVV.";
        isValid = false;
    } else {
        document.getElementById("cvvError").innerText = "";
    }

    // Show alert if there are errors
    if (!isValid) {
        alert("Please correct the highlighted errors in the form.");
    }

    return isValid;
}

function submitPayment() {
    if (validatePaymentForm()) {
        // Gather required data
        const userId = <?= json_encode($user_id) ?>;
        const totalPayment = <?= json_encode($totalPayment) ?>;

        // AJAX request to call the PHP function
        $.ajax({
            url: '/Usercontrol/paymentaction.php',
            type: 'POST',
            data: {
                action: 'processPayment',
                user_id: userId,
                totalPayment: totalPayment
            },
            success: function(response) {
                console.log("Server response:", response);
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    alert(jsonResponse.message);
                    $('#paymentModal').modal('hide'); // Hide the payment modal
                    $('#receiptModal').modal('show'); // Show the receipt modal
                } else {
                    alert(jsonResponse.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: ", textStatus, errorThrown);
                alert("An error occurred while processing the payment. Please try again.");
            }
        });
    }
}

function showReceiptModal() {
    if (validatePaymentForm()) {
        // Hide payment modal and show receipt modal
        $('#paymentModal').modal('hide');
        $('#receiptModal').modal('show');
    }
}

$(document).ready(function() {
    // Ensure the receipt modal is not shown when the payment modal is closed with the "X" button
    $('#paymentModal').on('hidden.bs.modal', function(e) {
        if ($(e.target).hasClass('show')) {
            return; // Only act if modal is manually closed, not by code
        }
    });

    // Function to handle the "Check Receipt" button click in the receipt modal
    $('#receiptModal .btn-success').on('click', function() {
        alert('Receipt confirmed. Thank you!');
        $('#receiptModal').modal('hide'); // Hide the receipt modal
        window.location.href = "User-panel.php"; // Redirect to user panel or another page
    });

    // Function to handle the "Go Back" button click in the receipt modal
    $('#receiptModal .btn-secondary').on('click', function() {
        $('#receiptModal').modal('hide');
    });
});


let policyRead = false; // Flag to track if policy was opened

// Function to mark policy as read
function markPolicyRead() {
    policyRead = true;
}

// Function to enable/disable Pay button based on checkbox state and policy read
function togglePayButton() {
    const policyCheck = document.getElementById('policyCheck');
    const payButton = document.getElementById('payButton');

    if (policyRead) {
        policyCheck.disabled = false; // Enable checkbox if policy has been read
        payButton.disabled = !policyCheck.checked; // Enable Pay button only if checkbox is checked
    } else {
        policyCheck.checked = false;
        payButton.disabled = true;
    }
}

// Listen for the No Return Policy modal close event
$('#policyModal').on('hidden.bs.modal', function () {
    if (policyRead) {
        document.getElementById('policyCheck').disabled = false; // Enable checkbox after modal is closed
    }
});
</script>

<?php $conn->close(); ?>

<style>
   /* Styling for the card image */
   #selectedCardImage {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-bottom: 15px;
    }
    .receipt-divider {
        border-top: 1px dashed #ccc;
        margin: 10px 0;
    }

    .modal-content {
        border-radius: 15px;
    }

    .modal-footer .btn {
        width: 100px; /* Adjusted for smaller buttons */
    }

    .modal-header .close {
        padding: 1rem;
    }

    .modal-title {
        font-size: 1rem;
    }
</style>
