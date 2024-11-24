<?php
include_once "../assets/config.php";
session_name("user_session");
session_start();

// Include the PHPMailer files
require '../otpphpmailer/PHPMailer-master/src/Exception.php';
require '../otpphpmailer/PHPMailer-master/src/PHPMailer.php';
require '../otpphpmailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging (remove or set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent any accidental output before JSON response
ob_start();

// Function to send payment receipt email using PHPMailer
function sendPaymentReceiptEmail($email, $totalPayment, $orderDetails) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'dinewatchph@gmail.com'; // Your Gmail email
        $mail->Password = 'ywed icaf boco yrzx'; // Your generated Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dinewatchph@gmail.com', 'Dine&Watch Support');
        $mail->addAddress($email); // Recipient email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Receipt - Dine&Watch';
        $mail->Body    = "<p>Dear User,</p>
                          <p>Your payment of <strong>$" . number_format($totalPayment, 2) . "</strong> has been successfully processed. Below are your order details:</p>
                          <p><strong>Order Details:</strong><br>" . nl2br($orderDetails) . "</p>
                          <p>Thank you for using Dine&Watch!</p>
                          <p>Best Regards,<br>Dine&Watch Support Team</p>";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to handle successful payment
function handleSuccessfulPayment($conn, $user_id, $totalPayment) {
    $conn->begin_transaction();

    try {
        // Update the status of data_reservations to 'Paid'
        $updateStatusQuery = "UPDATE data_reservations SET status = 'Paid' WHERE user_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer reservations to the reservations table
        $transferReservationsQuery = "
            INSERT INTO reservations (reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, created_at, updated_at)
            SELECT reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, created_at, updated_at
            FROM data_reservations
            WHERE user_id = ? AND status = 'Paid'
        ";
        $stmt = $conn->prepare($transferReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer order details to the orders table using reservation_id from data_reservations
        $transferOrdersQuery = "
            INSERT INTO orders (user_id, reservation_id, order_details, total_amount, order_time, status, created_at, updated_at, payment_method)
            SELECT 
                oi.user_id,
                dr.reservation_id,
                GROUP_CONCAT(
                    CONCAT(
                        'Product Name: ', pi.product_name, ' | Quantity: ', oi.quantity, ' | Price: ', oi.totalprice
                    ) SEPARATOR '; '
                ) AS order_details,
                ?,
                NOW(),
                'paid in advance',
                NOW(),
                NOW(),
                'Credit Card'
            FROM order_items oi
            JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Paid'
            JOIN product_items pi ON oi.product_id = pi.product_id
            WHERE oi.user_id = ?
            GROUP BY dr.reservation_id
        ";
        $stmt = $conn->prepare($transferOrdersQuery);
        $stmt->bind_param("di", $totalPayment, $user_id);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the last inserted order_id for linking to receipts
        $stmt->close();

        // Insert into the receipts table, linking each receipt to the correct order_id
        $receiptQuery = "
            INSERT INTO receipts (order_id, user_id, total_amount, payment_method)
            VALUES (?, ?, ?, 'Credit Card')
        ";
        $stmt = $conn->prepare($receiptQuery);
        $stmt->bind_param("iid", $order_id, $user_id, $totalPayment);
        $stmt->execute();
        $receipt_id = $stmt->insert_id; // Capture the inserted receipt_id for later use
        $stmt->close();

        // Insert each order item as a receipt item associated with the receipt_id and reservation_id
        $receiptItemsQuery = "
            INSERT INTO receipt_items (receipt_id, reservation_id, product_id, quantity, item_total_price, user_id)
            SELECT ?, dr.reservation_id, oi.product_id, oi.quantity, oi.totalprice, oi.user_id
            FROM order_items oi
            JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Paid'
            WHERE oi.user_id = ?
        ";
        $stmt = $conn->prepare($receiptItemsQuery);
        $stmt->bind_param("ii", $receipt_id, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception("No receipt items were inserted.");
        }
        $stmt->close();

        // Clear order_items and data_reservations for this user after successful processing
        $clearOrderItemsQuery = "DELETE FROM order_items WHERE user_id = ?";
        $stmt = $conn->prepare($clearOrderItemsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $clearReservationsQuery = "DELETE FROM data_reservations WHERE user_id = ? AND status = 'Paid'";
        $stmt = $conn->prepare($clearReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Fetch user email for sending payment receipt
        $getUserEmailQuery = "SELECT email FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($getUserEmailQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        // Send the payment receipt email
        $orderDetails = "Your total payment: $" . number_format($totalPayment, 2); // You can customize this based on your order details
        sendPaymentReceiptEmail($email, $totalPayment, $orderDetails);

        $conn->commit();
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'success', 'message' => 'Payment processed, status updated, and data transferred successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}

if (isset($_POST['user_id']) && isset($_POST['totalPayment'])) {
    $user_id = intval($_POST['user_id']);
    $totalPayment = floatval($_POST['totalPayment']);

    if ($user_id > 0 && $totalPayment > 0) {
        handleSuccessfulPayment($conn, $user_id, $totalPayment);
    } else {
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
    }
} else {
    ob_end_clean(); // Clean the buffer to ensure only JSON is sent
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
}

$conn->close();
?>
