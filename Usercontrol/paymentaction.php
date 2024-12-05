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
// Function to send receipt email
function sendReceiptEmail($email, $receiptDetails) {
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
        $mail->Subject = 'Your Dine&Watch Receipt';
        $mail->Body    = "<p>Dear User,</p>
                          <p>Thank you for your purchase! Here are the details of your transaction:</p>
                          <p><strong>Receipt Details:</strong><br>{$receiptDetails}</p>
                          <p>Best Regards,<br>Dine&Watch Support Team</p>";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to handle successful payment and generate receipt
function handleSuccessfulPayment($conn, $user_id, $totalPayment) {
    $conn->begin_transaction();
    
    try {
        // Update the status of data_reservations to 'Reserved'
        $updateStatusQuery = "UPDATE data_reservations SET status = 'Reserved' WHERE user_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer reservations to the reservations table with status 'Complete'
        $transferReservationsQuery = "
            INSERT INTO reservations (reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, created_at, updated_at)
            SELECT reservation_id, user_id, table_id, reservation_date, reservation_time, 'Reserved', custom_note, created_at, updated_at
            FROM data_reservations
            WHERE user_id = ? AND status = 'Reserved'
        ";
        $stmt = $conn->prepare($transferReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer order details to the orders table
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
            JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Reserved'
            JOIN product_items pi ON oi.product_id = pi.product_id
            WHERE oi.user_id = ?
            GROUP BY dr.reservation_id
        ";
        $stmt = $conn->prepare($transferOrdersQuery);
        $stmt->bind_param("di", $totalPayment, $user_id);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the last inserted order_id
        $stmt->close();

        // Insert into the receipts table
        $receiptQuery = "
            INSERT INTO receipts (order_id, user_id, total_amount, payment_method)
            VALUES (?, ?, ?, 'Credit Card')
        ";
        $stmt = $conn->prepare($receiptQuery);
        $stmt->bind_param("iid", $order_id, $user_id, $totalPayment);
        $stmt->execute();
        $receipt_id = $stmt->insert_id; // Get the last inserted receipt_id
        $stmt->close();

        // Insert each order item as a receipt item
        $receiptItemsQuery = "
            INSERT INTO receipt_items (receipt_id, reservation_id, product_id, quantity, item_total_price, user_id)
            SELECT ?, dr.reservation_id, oi.product_id, oi.quantity, oi.totalprice, oi.user_id
            FROM order_items oi
            JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Reserved'
            WHERE oi.user_id = ?;
        ";
        $stmt = $conn->prepare($receiptItemsQuery);
        $stmt->bind_param("ii", $receipt_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Deduct the ordered quantity from product_items
        $deductQuantityQuery = "
            UPDATE product_items pi
            JOIN order_items oi ON pi.product_id = oi.product_id
            SET pi.quantity = pi.quantity - oi.quantity
            WHERE oi.user_id = ?;
        ";
        $stmt = $conn->prepare($deductQuantityQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Clear order_items and data_reservations for this user
        $clearOrderItemsQuery = "DELETE FROM order_items WHERE user_id = ?";
        $stmt = $conn->prepare($clearOrderItemsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $clearReservationsQuery = "DELETE FROM data_reservations WHERE user_id = ? AND status = 'Reserved'";
        $stmt = $conn->prepare($clearReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        // Fetch product details from the receipt_items table
        $receiptDetailsQuery = "
            SELECT pi.product_name, ri.quantity, ri.item_total_price
            FROM receipt_items ri
            JOIN product_items pi ON ri.product_id = pi.product_id
            WHERE ri.user_id = ? AND ri.receipt_id = ?
        ";
        $stmt = $conn->prepare($receiptDetailsQuery);
        $stmt->bind_param("ii", $user_id, $receipt_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Build the receipt details to include product info
        $receiptDetails = "Order ID: {$order_id}<br>Total Payment: {$totalPayment} PHP<br><br><strong>Product Details:</strong><br>";
        while ($row = $result->fetch_assoc()) {
            $receiptDetails .= "Product: {$row['product_name']} | Quantity: {$row['quantity']} | Price: {$row['item_total_price']} PHP<br>";
        }

        // Send receipt email
        $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $stmt->close();

        if (sendReceiptEmail($email, $receiptDetails)) {
            echo json_encode(['status' => 'success', 'message' => 'Payment processed and receipt email sent!']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Payment processed, but email could not be sent.']);
        }

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}





// Handle payment processing
if (isset($_POST['user_id']) && isset($_POST['totalPayment'])) {
    $user_id = intval($_POST['user_id']);
    $totalPayment = floatval($_POST['totalPayment']);

    if ($user_id > 0 && $totalPayment > 0) {
        handleSuccessfulPayment($conn, $user_id, $totalPayment);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
}

$conn->close();

?>
