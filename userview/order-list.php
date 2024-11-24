<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

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

    // Display total payment, set to "P 0.00" if no payment
    echo '<div class="container mt-4">';
    echo '<h4 class="text-center" id="totalPayment">Total Payment: &#8369; ' . (is_null($totalPayment) || $totalPayment == 0 ? '0.00' : number_format($totalPayment, 2)) . '</h4>';
    echo '</div>';

    // Fetch categories and orders
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

        // Loop through each category
        while ($category = $categoriesResult->fetch_assoc()) {
            $categoryName = htmlspecialchars($category['category_name']);
            echo "<h3 class='category-separator mt-4'>$categoryName</h3>";
            echo '<div class="row">';

            if (isset($ordersByCategory[$categoryName])) {
                foreach ($ordersByCategory[$categoryName] as $order) {
                    echo '<div class="col-md-4 mb-4" id="order-item-' . $order['order_item_id'] . '">';
                    echo '<div class="card h-100">';
                    echo '<img src="' . htmlspecialchars($order['product_image']) . '" alt="' . htmlspecialchars($order['product_name']) . '" class="card-img-top" style="height: 150px; object-fit: cover;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($order['product_name']) . '</h5>';
                    echo '<p class="card-text">Total Price: &#8369; <span id="total-price-' . $order['order_item_id'] . '">' . number_format($order['price'] * $order['quantity'], 2) . '</span></p>';
                    echo '<p class="card-text">Quantity: 
                            <button class="btn btn-qtyminus" onclick="changeQuantity(' . $order['order_item_id'] . ', ' . $order['price'] . ', -1)">
                                <i class="fa fa-minus" aria-hidden="true"></i>
                            </button>
                            <span id="quantity-' . $order['order_item_id'] . '">' . $order['quantity'] . '</span>
                            <button class="btn btn-qtyplus" onclick="changeQuantity(' . $order['order_item_id'] . ', ' . $order['price'] . ', 1)">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </button>
                          </p>';
                    echo '</div>';
                    echo '<div class="card-footer text-center">';
                    echo '<button class="btn btn-danger" onclick="openDeleteConfirmation(' . $order['order_item_id'] . ')">Delete</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }

            // Add "Add Foods" button at the end of each category section
            echo '<div class="col-md-4 mb-4">';
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
    <button type="button" class="btn proceed-button" id="proceedbutton" onclick="submitReservationForm()">Complete</button> 
    </form>

    </div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="table-alert-modal" tabindex="-1" role="dialog" aria-labelledby="tableAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tableAlertModalLabel">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center" id="tableAlertlistMessage">
                <!-- Success or error message will be displayed here with icon -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <p>Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteItem()">Delete</button>
            </div>
        </div>
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
    const confirmModal = document.getElementById('confirmDeleteModal');
    confirmModal.setAttribute('data-order-item-id', orderItemId); // Set order_item_id in modal data attribute
    $('#confirmDeleteModal').modal('show'); // Show the confirmation modal
}

function openDeleteConfirmation(orderItemId) {
    const confirmModal = document.getElementById('confirmDeleteModal');
    confirmModal.setAttribute('data-order-item-id', orderItemId);
    $('#confirmDeleteModal').modal('show');
}

function confirmDeleteItem() {
    const confirmModal = document.getElementById('confirmDeleteModal');
    const orderItemId = confirmModal.getAttribute('data-order-item-id');

    if (!orderItemId) {
        showAlertlist("No item selected for deletion.", "error");
        return;
    }

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
            $('#confirmDeleteModal').modal('hide');
        } else {
            showAlertlist("Error: " + data.message, 'error');
        }
    })
    .catch(error => {
        showAlertlist("An error occurred: " + error.message, "error");
    });
}
function showAlertlist(message, type) {
    const alertMessage = document.getElementById('tableAlertlistMessage');
    alertMessage.innerHTML = `
        <i class="fa ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'} fa-3x mr-2" aria-hidden="true"></i>
        <span style="font-size: 15px;">${message}</span>`;

    // Add background color based on type to modal body only
    const modalBody = document.querySelector('#table-alert-modal .modal-body');
    modalBody.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da';

    $('#table-alert-modal').modal('show');
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

<style>
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
</style>
