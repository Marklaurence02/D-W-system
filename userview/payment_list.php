<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch order items for the user
$orderQuery = "
    SELECT oi.order_item_id, oi.order_id, oi.quantity, pi.product_name, pi.price
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
    // Compute total price for each item
    $row['totalprice'] = $row['price'] * $row['quantity']; 
    $orders[] = $row;

    // Add to total payment
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
<style>
    .progress-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
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

    /* Responsive styles */
    @media (max-width: 768px) {
        .progress-step {
            width: 25px;
            height: 25px;
            margin: 0 5px;
        }

        .progress-line {
            width: 30px;
        }

        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        .modal-footer .btn {
            width: auto; /* Allow buttons to adjust width */
        }

        .container.mt-5 {
            padding: 0 15px; /* Add padding for smaller screens */
        }

        .card {
            margin-bottom: 20px;
        }

        .text-right {
            text-align: center; /* Center align text on smaller screens */
        }
    }

    .modal-body {
        max-height: 400px; /* Set a maximum height for the modal body */
        overflow-y: auto;  /* Enable vertical scrolling */
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

    /* Responsive styles */
    @media (max-width: 768px) {
        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }
    }

    /* Make scrollbars invisible */
    .modal-body {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
    }

    .modal-body::-webkit-scrollbar { 
        width: 0px; 
        background: transparent; /* Chrome/Safari/Webkit */
    }
</style>

<div class="progress-container">
<div class="progress-step active"onclick="ordertable()">1</div>
<div class="progress-line active"></div>
    <div class="progress-step active"onclick="order_list()">2</div>
    <div class="progress-line active"></div>
    <div class="progress-step active" onclick="submitReservationForm()">3</div>
    <div class="progress-line active"></div>
    <div class="progress-step active" onclick="savedreservation()">4</div>
    <div class="progress-line active"></div>
    <div class="progress-step active">5</div>
</div>
<div class="container mt-5">
    <h2 class="text-center mb-4">Payment Receipt</h2>

    <!-- Order Items Section -->
    <div class="card mb-4" style="background-color: #D9D9D9; border-radius: 10px;">
        <div class="card-header">
            <h4>Your Orders</h4>
        </div>
        <div class="card-body">
            <?php if (count($orders) > 0): ?>
                <div class="table-responsive">
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
                </div>
            <?php else: ?>
                <p>No order items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reservation Details Section -->
    <div class="card mb-4" style="background-color: #D9D9D9; border-radius: 10px;">
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
            </div>
            <div class="modal-body" style="padding: 10px; font-size: 0.9rem;">
            <p style="margin-bottom: 5px;">Please review your order and reservation details carefully before completing the payment.</p>
                <ul style="padding-left: 20px; margin-bottom: 0;">
                    <li>Make sure the items and quantities are correct.</li>
                    <li>If there are any issues with the reservation, contact our support team.</li>
                    <li>All sales are final. No refunds or exchanges.</li>
                    <li>Products and services are sold "as-is" without warranties of any kind.</li>
                    <li>By proceeding with the payment, you agree to accept these terms.</li>
                    <li>For additional orders, please inform the restaurant counter.</li>
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
                <button class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#paymentModal" style="width: fit-content;">Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                        <div class="row mb-1 small">
                            <div class="col-6"><strong>ID:</strong> <?= htmlspecialchars($reservation['reservation_id']) ?></div>
                            <div class="col-6"><strong>Table:</strong> <?= htmlspecialchars($reservation['table_id']) ?></div>
                            <div class="col-6"><strong>Date:</strong> <?= htmlspecialchars($reservation['reservation_date']) ?></div>
                            <div class="col-6"><strong>Time:</strong> <?= date('g:i A', strtotime($reservation['reservation_time'])) ?></div>
                            <div class="col-6"><strong>Status:</strong> <?= htmlspecialchars($reservation['status']) ?></div>
                            <div class="col-6"><strong>Note:</strong> <?= htmlspecialchars($reservation['custom_note']) ?></div>
                        </div>
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
                        <a href="#" data-toggle="modal" data-target="#policyModal" onclick="markPolicyRead()">No Refund Policy</a>.
                    </label>
                </div>
                <style>
                    @media (max-width: 768px) {
                        .form-check {
                            text-align: center; /* Center align text on smaller screens */
                        }
                        .form-check-label {
                            display: block; /* Make label block-level for better spacing */
                        }
                    }
                </style>
                <!-- Pay Button -->
                <button type="button" class="btn btn-success btn-sm" id="payButton" onclick="showPasswordModal()" disabled>Pay</button>
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
                <h5 class="modal-title" id="policyModalLabel">No Refund Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 10px; font-size: 0.9rem;">
                <p style="margin-bottom: 5px;">Please carefully read and understand our No Refund Policy:</p>
                <ul style="padding-left: 20px; margin-bottom: 0;">
                    <li>All sales are final. No refunds or exchanges.</li>
                    <li>Products and services are sold "as-is" without warranties of any kind.</li>
                    <li>By proceeding with the payment, you agree to accept these terms.</li>
                    <li>For additional orders, please inform the restaurant counter.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" style="width:fit-content">I Understand</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="passwordModalLabel">Enter Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="form-group">
                        <label for="userPassword" class="small">Password</label>
                        <input type="password" class="form-control" id="userPassword" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="validatePassword()">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<!-- Custom Loader -->
<div id="customLoader" class="loader-overlay">
    <div class="loader" id="loader"></div>
    <div class="checkmark hidden" id="checkmark">
        
    </div>
</div>


<!-- Payment Modal with Card Options -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white p-2">
                <h6 class="modal-title" id="paymentModalLabel">Complete Payment</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <form id="paymentForm" onsubmit="return validatePaymentForm()">
                    <!-- Card Image Display on Top -->
                    <div class="text-center mb-2">
                        <img id="selectedCardImage" src="../Images/visa.png" alt="Selected Card" style="width: 80px; height: auto;">
                    </div>

                    <!-- Card Type Selector -->
                    <div class="form-group mb-2">
                        <label for="cardType">Select Card Type</label>
                        <select class="form-control form-control-sm" id="cardType" onchange="updateCardDetails()">
                            <option value="" disabled selected>Select a card for testing</option>
                            <option value="visa" data-image="../Images/visa.png" data-card-number="4111 1111 1111 1111" data-cvv="123">Visa - 4111 1111 1111 1111</option>
                            <option value="mastercard" data-image="../Images/mastercard.png" data-card-number="5555 5555 5555 4444" data-cvv="456">MasterCard - 5555 5555 5555 4444</option>
                            <option value="amex" data-image="../Images/amex.png" data-card-number="3782 822463 10005" data-cvv="789">American Express - 3782 822463 10005</option>
                            <option value="gcashcard" data-image="../Images/gcashcard.png" data-card-number="6019 1234 5678 9012" data-cvv="012">GCash Card - 6019 1234 5678 9012</option>
                        </select>
                    </div>

                    <!-- Card Information Section -->
                    <div class="form-group mb-2">
                        <label for="cardName">Cardholder Name</label>
                        <input type="text" class="form-control form-control-sm" id="cardName" placeholder="Name on card" value="<?= htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']) ?>" readonly>
                    </div>
                    <div class="form-group mb-2">
                        <label for="cardNumber">Card Number <i class="fas fa-info-circle" data-toggle="tooltip" title="Enter your 16-digit card number."></i></label>
                        <input type="text" class="form-control form-control-sm" id="cardNumber" placeholder="Card number" required>
                        <small class="form-text text-danger" id="cardNumberError"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-2">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" class="form-control form-control-sm" id="expiryDate" placeholder="MM/YY" required>
                            <small class="form-text text-danger" id="expiryDateError"></small>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control form-control-sm" id="cvv" placeholder="CVV" required>
                            <small class="form-text text-danger" id="cvvError"></small>
                        </div>
                    </div>
                    <!-- Updated button to show receipt modal -->
                    <button type="button" class="btn btn-primary btn-sm btn-block" onclick="showReceiptModal()">Check Receipt</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php $conn->close(); ?>


<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="alertMessage">
                <!-- Dynamic alert message will be shown here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const expiryDateInput = document.getElementById('expiryDate');
        const now = new Date();
        const currentMonth = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const currentYear = String(now.getFullYear()).slice(-2); // Get last two digits of the year

        // Set the initial value to the current month and year
        expiryDateInput.value = `${currentMonth}/${currentYear}`;

        // Add event listener for validation
        expiryDateInput.addEventListener('input', function() {
            validateExpiryDate(this);
        });
    });

    function validateExpiryDate(input) {
        const now = new Date();
        const currentMonth = now.getMonth() + 1; // JavaScript months are zero-based
        const currentYear = now.getFullYear() % 100; // Get last two digits of the year

        // Remove any non-digit characters
        let value = input.value.replace(/[^\d]/g, '');

        // Ensure proper formatting
        if (value.length > 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }

        // Validate month
        let month = parseInt(value.slice(0, 2), 10);
        let year = parseInt(value.slice(3), 10);

        // Ensure month is between 01 and 12
        if (month < 1 || month > 12) {
            month = currentMonth;
        }

        // Format month with leading zero if needed
        const formattedMonth = String(month).padStart(2, '0');

        // Validate year
        if (isNaN(year) || year < currentYear) {
            year = currentYear;
        }

        // Check if the card is expired
        if (year === currentYear && month < currentMonth) {
            month = currentMonth;
        }

        // Set the formatted value
        input.value = `${formattedMonth}/${String(year).padStart(2, '0')}`;

        // Optional: Add visual feedback for expiration
        const expiryDateError = document.getElementById('expiryDateError');
        if (year < currentYear || (year === currentYear && month < currentMonth)) {
            input.classList.add('is-invalid');
            expiryDateError.textContent = 'Card has expired';
        } else {
            input.classList.remove('is-invalid');
            expiryDateError.textContent = '';
        }
    }

    // Modify the existing validation function to include expiry date check
    function validatePaymentForm() {
        let isValid = true;
        const cardNumberInput = document.getElementById("cardNumber");
        const expiryDateInput = document.getElementById("expiryDate");
        const cvvInput = document.getElementById("cvv");

        const cardNumber = cardNumberInput.value.trim();
        const expiryDate = expiryDateInput.value.trim();
        const cvv = cvvInput.value.trim();

        const cardNumberRegex = /^\d{4} \d{4} \d{4} \d{4}$/;
        const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        const cvvRegex = /^\d{3,4}$/;

        const now = new Date();
        const currentMonth = now.getMonth() + 1;
        const currentYear = now.getFullYear() % 100;

        // Clear previous error highlights and messages
        cardNumberInput.classList.remove("is-invalid");
        expiryDateInput.classList.remove("is-invalid");
        cvvInput.classList.remove("is-invalid");
        document.getElementById("cardNumberError").textContent = "";
        document.getElementById("expiryDateError").textContent = "";
        document.getElementById("cvvError").textContent = "";

        // Validate card number (existing code)
        if (!cardNumberRegex.test(cardNumber)) {
            document.getElementById("cardNumberError").textContent = "Enter a valid 16-digit card number.";
            cardNumberInput.classList.add("is-invalid");
            isValid = false;
        }

        // Validate expiry date with additional checks
        if (!expiryDateRegex.test(expiryDate)) {
            document.getElementById("expiryDateError").textContent = "Enter a valid expiry date in MM/YY format.";
            expiryDateInput.classList.add("is-invalid");
            isValid = false;
        } else {
            const month = parseInt(expiryDate.slice(0, 2), 10);
            const year = parseInt(expiryDate.slice(3), 10);

            if (year < currentYear || (year === currentYear && month < currentMonth)) {
                document.getElementById("expiryDateError").textContent = "Card has expired";
                expiryDateInput.classList.add("is-invalid");
                isValid = false;
            }
        }

        // Validate CVV (existing code)
        if (!cvvRegex.test(cvv)) {
            document.getElementById("cvvError").textContent = "Enter a valid 3 or 4-digit CVV.";
            cvvInput.classList.add("is-invalid");
            isValid = false;
        }

        return isValid;
    }

    // Use SweetAlert for notifications
    function showAlert(message, type = 'info') {
        let icon;
        switch (type) {
            case 'success':
                icon = 'success';
                break;
            case 'error':
                icon = 'error';
                break;
            default:
                icon = 'info';
        }

        Swal.fire({
            title: type.charAt(0).toUpperCase() + type.slice(1),
            text: message,
            icon: icon,
            confirmButtonText: 'OK'
        });
    }

    function updateCardDetails() {
        const cardTypeSelect = document.getElementById("cardType");
        const selectedOption = cardTypeSelect.options[cardTypeSelect.selectedIndex];
        
        const selectedCardImage = document.getElementById("selectedCardImage");
        const cardNumberInput = document.getElementById("cardNumber");
        const cvvInput = document.getElementById("cvv");

        selectedCardImage.src = selectedOption.getAttribute("data-image") || "../Images/visa.png";
        cardNumberInput.value = selectedOption.getAttribute("data-card-number") || "";
        cvvInput.value = selectedOption.getAttribute("data-cvv") || "";
    }

    function showPasswordModal() {
        $('#passwordModal').modal('show');
    }

    function validatePassword() {
        const passwordInput = document.getElementById('userPassword');
        const password = passwordInput.value.trim();

        if (password.length === 0) {
            showAlert("Please enter your password.", 'error');
            passwordInput.focus();
            return false;
        }

        if (password.length < 8) {
            showAlert("Password must be at least 8 characters long.", 'error');
            passwordInput.focus();
            return false;
        }

        // AJAX request to validate password on the server
        $.ajax({
            url: '/Usercontrol/passwordVerification.php',
            type: 'POST',
            data: {
                action: 'verifyPassword',
                password: password
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    showAlert("Password verified successfully.", 'success');
                    $('#passwordModal').modal('hide');
                    submitPayment();
                } else {
                    showAlert(result.message || "Password verification failed.", 'error');
                }
            },
            error: function() {
                showAlert("An error occurred while verifying the password. Please try again.", 'error');
            }
        });

        return false; // Prevent form submission until verification is complete
    }


    function submitPayment() {
    if (validatePaymentForm()) {
        showAlert("Processing your payment, please wait...", 'info');
        showLoadingAnimation(); // Show the loading animation before making the AJAX request

        // Ensure these variables are sanitized and safe for embedding in JavaScript
        const userId = <?= json_encode($user_id) ?>;
        const totalPayment = <?= json_encode($totalPayment) ?>;

        $.ajax({
            url: '/Usercontrol/paymentaction.php',
            type: 'POST',
            data: {
                action: 'processPayment',
                user_id: userId,
                totalPayment: totalPayment
            },
            success: function(response) {
                console.log("Server response:", response); // Log the raw response for inspection
                try {
                    const jsonResponse = JSON.parse(response);
                    // Keep the loading animation for 10 seconds before hiding and showing the alert
                    setTimeout(() => {
                        hideLoadingAnimation(); // Hide the loading animation after 10 seconds
                        if (jsonResponse.status === 'success') {
                            showAlert(jsonResponse.message, 'success');
                            $('#paymentModal').modal('hide');
                            $('#receiptModal').modal('show');
                            setTimeout(() => {
                                redirectToUserPanel();
                            }, 3000); // Adjust the delay as needed
                        } else {
                            showAlert(jsonResponse.message, 'error');
                        }
                    }, 10000); // Delay of 10 seconds
                } catch (error) {
                    console.error("Response parsing error:", error);
                    setTimeout(() => {
                        hideLoadingAnimation();
                        showAlert("Unexpected response format from the server.", 'error');
                    }, 10000); // Delay of 10 seconds
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                setTimeout(() => {
                    hideLoadingAnimation();
                    showAlert("An error occurred while processing the payment. Please try again.", 'error');
                }, 10000); // Delay of 10 seconds
            }
        });
    }
}


function showReceiptModal() {
        if (validatePaymentForm()) {
            $('#paymentModal').modal('hide');
            $('#receiptModal').modal('show');
        }
    }

    let policyRead = false;

    function markPolicyRead() {
        policyRead = true;
    }

    function togglePayButton() {
        const policyCheck = document.getElementById('policyCheck');
        const payButton = document.getElementById('payButton');

        if (policyRead) {
            policyCheck.disabled = false;
            payButton.disabled = !policyCheck.checked;
        } else {
            policyCheck.checked = false;
            payButton.disabled = true;
        }
    }

    $('#policyModal').on('hidden.bs.modal', function () {
        if (policyRead) {
            document.getElementById('policyCheck').disabled = false;
        }
    });
    
    function showLoadingAnimation() {
    const loader = document.getElementById('customLoader');
    if (loader) {
        loader.style.display = 'flex'; // Show the loader
        console.log("Loader displayed."); // Debug log
    } else {
        console.error("Loader element not found.");
    }
}

function hideLoadingAnimation() {
    const loader = document.getElementById('customLoader');
    if (loader) {
        loader.style.display = 'none'; // Hide the loader
        console.log("Loader hidden."); // Debug log
    } else {
        console.error("Loader element not found.");
    }
}

// Example usage: call showLoadingAnimation() and hideLoadingAnimation() during page load or AJAX calls
document.addEventListener('DOMContentLoaded', () => {
    showLoadingAnimation();
    setTimeout(hideLoadingAnimation, 10000); // Simulate loading and hide after 3 seconds
});


// Redirect to the user panel
function redirectToUserPanel() {
    window.location.href = 'User-panel.php'; // Adjust the path as needed
}


</script>


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

    .loader-overlay {
    display: none; /* Hidden by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    justify-content: center;
    align-items: center;
}

.loader {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid blue;
    border-right: 16px solid green;
    border-bottom: 16px solid red;
    border-left: 16px solid pink;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Add hover effects to buttons */
.btn:hover {
    opacity: 0.8;
    transition: opacity 0.3s;
}

/* Improve form validation feedback */
.is-invalid {
    border-color: #dc3545;
    background-color: #f8d7da;
}

.form-text.text-danger {
    color: #dc3545;
}

/* Tooltip styling */
.tooltip-inner {
    background-color: #333;
    color: #fff;
    border-radius: 4px;
    padding: 5px;
}

.tooltip.bs-tooltip-top .arrow::before {
    border-top-color: #333;
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

</style>

<script>
    // Add tooltips for better guidance
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Show loading spinner on form submission
    function showLoadingSpinner() {
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-primary';
        spinner.setAttribute('role', 'status');
        spinner.innerHTML = '<span class="sr-only">Loading...</span>';
        document.body.appendChild(spinner);
    }

    // Example usage: call showLoadingSpinner() when a form is submitted
    document.getElementById('paymentForm').addEventListener('submit', function() {
        showLoadingSpinner();
    });
</script>
