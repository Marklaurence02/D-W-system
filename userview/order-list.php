<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Add the highlighted section here
?>

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

    .category-separator {
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 10px;
        font-size: 1.25rem;
        color: #333;
    }
    .card {
        transition: transform 0.3s;
    }
    .card:hover {
        transform: scale(1.05);
    }
    .card-img-top {
        height: 150px;
        object-fit: cover;
    }
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .card-footer {
        text-align: center;
    }
</style>

<div class="progress-container">
    <div class="progress-step active" onclick="ordertable()">1</div>
    <div class="progress-line active"></div>
    <div class="progress-step active">2</div>
    <div class="progress-line"></div>
    <div class="progress-step" onclick="submitReservationForm()">3</div>
    <div class="progress-line"></div>
    <div class="progress-step">4</div>
    <div class="progress-line"></div>
    <div class="progress-step">5</div>
</div>

<h4 class="text-center">Pick Food</h4>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record'])) {
    $userId = $_SESSION['user_id'];

    // Calculate the total payment for all orders
    $totalPaymentQuery = "
        SELECT SUM(oi.quantity * pi.price) AS total_payment
        FROM order_items oi
        JOIN product_items pi ON oi.product_id = pi.product_id
        WHERE oi.user_id = ?";
    $stmt = $conn->prepare($totalPaymentQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($totalPayment);
    $stmt->fetch();
    $stmt->close();

    echo '<div class="container mt-4">';
    echo '<h4 class="text-center" id="totalPayment">Total Payment: &#8369; ' . (is_null($totalPayment) || $totalPayment == 0 ? '0.00' : number_format($totalPayment, 2)) . '</h4>';
    echo '</div>';

    $categoriesQuery = "SELECT * FROM product_categories ORDER BY category_name DESC";
    $categoriesResult = $conn->query($categoriesQuery);

    if ($categoriesResult->num_rows > 0) {
        echo '<div class="container" id="orderContainer">';

        $ordersQuery = "
            SELECT oi.*, pi.product_name, pi.product_image, pi.price, pc.category_name
            FROM order_items oi
            JOIN product_items pi ON oi.product_id = pi.product_id
            JOIN product_categories pc ON pi.category_id = pc.category_id
            WHERE oi.user_id = ?
            ORDER BY pc.category_name DESC, oi.order_item_id";
        
        $stmt = $conn->prepare($ordersQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $ordersResult = $stmt->get_result();

        $ordersByCategory = [];
        while ($order = $ordersResult->fetch_assoc()) {
            $ordersByCategory[$order['category_name']][] = $order;
        }
        $stmt->close();

        while ($category = $categoriesResult->fetch_assoc()) {
            $categoryName = htmlspecialchars($category['category_name']);
            echo "<h3 class='category-separator mt-4'>$categoryName</h3>";
            echo '<div class="row">';

            if (isset($ordersByCategory[$categoryName])) {
                foreach ($ordersByCategory[$categoryName] as $order) {
                    echo '<div class="col-md-4 col-sm-6 col-xs-12 mb-4" id="order-item-' . $order['order_item_id'] . '">';
                    echo '<div class="card h-100">';
                    echo '<img src="' . htmlspecialchars($order['product_image']) . '" alt="' . htmlspecialchars($order['product_name']) . '" class="card-img-top">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($order['product_name']) . '</h5>';
                    echo '<p class="card-text">Total Price: &#8369; <span id="total-price-' . $order['order_item_id'] . '">' . number_format($order['price'] * $order['quantity'], 2) . '</span></p>';
                    echo '<p class="card-text">Quantity: 
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-qtyminus btn-sm mx-1" onclick="changeQuantity(' . $order['order_item_id'] . ', ' . $order['price'] . ', -1)">
                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                </button>
                                <span id="quantity-' . $order['order_item_id'] . '" class="mx-2">' . $order['quantity'] . '</span>
                                <button class="btn btn-qtyplus btn-sm mx-1" onclick="changeQuantity(' . $order['order_item_id'] . ', ' . $order['price'] . ', 1)">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                          </p>';
                    echo '</div>';
                    echo '<div class="card-footer">';
                    echo '<button class="btn btn-danger" onclick="openDeleteConfirmation(' . $order['order_item_id'] . ')">Delete</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }

            echo '<div class="col-md-4 col-sm-6 col-xs-12 mb-4">';
            echo '<div class="card h-100" style="background-color: #f0f0f0;">';
            echo '<div class="d-flex flex-column justify-content-center align-items-center" style="height: 100%;">';
            echo '<button class="btn btn-plus" onclick="ordertable()" style="width: 100%; height: 100%; font-size: 2rem;"><i class="fa fa-plus" aria-hidden="true"></i></button>';
            echo '<h5 class="card-title mt-2">Add Foods</h5>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
    } else {
        echo "No categories found.";
    }
    $categoriesResult->close();
}
?>

<!-- button-->
<div class="container mt-4">
    <div class="d-flex justify-content-end">
        <button class="btn proceed-button" onclick="ordertable()">Back</button>
        <form id="reservationForm" method="post" class="ml-2">
            <input type="hidden" name="record" value="1">
            <button type="button" class="btn proceed-button" onclick="submitReservationForm()">Proceed</button> 
        </form>
    </div>
</div>

<script>
function changeQuantity(orderItemId, unitPrice, change) {
    const quantityElem = document.getElementById(`quantity-${orderItemId}`);
    const priceElem = document.getElementById(`total-price-${orderItemId}`);
    let currentQuantity = parseInt(quantityElem.textContent) + change;

    if (currentQuantity < 1) {
        showAlertlist("Quantity can't be zero. Please press delete to remove the item.", 'error');
        return;
    }

    const newTotalPrice = (currentQuantity * unitPrice).toFixed(2);
    quantityElem.textContent = currentQuantity;
    priceElem.textContent = newTotalPrice;

    updateTotalPayment(unitPrice * change);

    $.ajax({
        url: '/Usercontrol/updatequantity.php',
        type: 'POST',
        data: {
            order_item_id: orderItemId,
            quantity: currentQuantity,
            total_price: newTotalPrice
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                showAlertlist('Quantity updated successfully.', 'success');
            } else {
                showAlertlist(result.message, 'error');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlertlist('An error occurred: ' + textStatus, 'error');
        }
    });
}


// Update total payment dynamically

function updateTotalPayment(amountChange) {
    const totalPaymentElem = document.getElementById('totalPayment');
    let currentTotal = parseFloat(totalPaymentElem.innerText.replace('Total Payment: ₱ ', '').replace(/,/g, ''));

    if (isNaN(currentTotal)) currentTotal = 0; // Set to 0 if NaN

    let newTotal = (currentTotal + amountChange).toFixed(2);
    totalPaymentElem.innerText = 'Total Payment: ₱ ' + (newTotal > 0 ? parseFloat(newTotal).toLocaleString() : '0.00');
}


// Store the order_item_id in the modal's data attribute and show the modal
function openDeleteConfirmation(orderItemId) {
    Swal.fire({
        title: 'Confirm Deletion',
        text: "Are you sure you want to delete this item?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'confirm-delete-modal', // Optional: Add a custom class for styling
        }
    }).then((result) => {
        if (result.isConfirmed) {
            confirmDeleteItem(orderItemId);
        }
    });
}

function confirmDeleteItem(orderItemId) {
    fetch('/Usercontrol/DeleteOrder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_item_id: orderItemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlertlist(data.message, 'success');
            document.getElementById(`order-item-${orderItemId}`).remove();
            updateTotalPayment(-parseFloat(data.deletedAmount)); // Update payment by removed item's amount
        } else {
            showAlertlist("Error: " + data.message, 'error');
        }
    })
    .catch(error => {
        showAlertlist("An error occurred: " + error.message, "error");
    });
}

function showAlertlist(message, type) {
    // Use SweetAlert for notifications
    Swal.fire({
        icon: type === 'success' ? 'success' : 'error',
        title: type === 'success' ? 'Success' : 'Error',
        text: message,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'table-alert-modal', // Keep the existing class for styling
        }
    });
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
                    $('#proceedbutton')
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

