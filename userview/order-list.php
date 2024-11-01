<?php
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

    echo '<div class="container mt-4">';
    echo '<h4 class="text-center">Total Payment: &#x20B1; ' . number_format($totalPayment, 2) . '</h4>';
    echo '</div>';

    // Fetch all categories in descending order
    $categoriesQuery = "SELECT * FROM product_categories ORDER BY category_name DESC";
    $categoriesResult = $conn->query($categoriesQuery);

    if ($categoriesResult->num_rows > 0) {
        echo '<div class="container" id="orderContainer">'; // Begin the main container for dynamic order content

        // Fetch all orders for the user in descending order
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

        // Loop through each category and output order items
        while ($category = $categoriesResult->fetch_assoc()) {
            $categoryName = htmlspecialchars($category['category_name']);
            echo "<h3 class='category-separator mt-4'>$categoryName</h3>";
            echo '<div class="row">'; // Start new category group
            
            if (isset($ordersByCategory[$categoryName])) {
                foreach ($ordersByCategory[$categoryName] as $order) {
                    echo '<div class="col-md-4 mb-4" id="order-item-' . $order['order_item_id'] . '">';
                    echo '<div class="card h-100">';
                    echo '<img src="' . htmlspecialchars($order['product_image']) . '" alt="' . htmlspecialchars($order['product_name']) . '" class="card-img-top" style="height: 150px; object-fit: cover;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($order['product_name']) . '</h5>';
                    echo '<p class="card-text">Total Price: &#x20B1; <span id="total-price-' . $order['order_item_id'] . '">' . number_format($order['price'] * $order['quantity'], 2) . '</span></p>';
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
                    echo '</div>'; // Close card footer
                    echo '</div>'; // Close card
                    echo '</div>'; // Close column
                }
            }

            // Add "Add Foods" button at the end of each category section
            echo '<div class="col-md-4 mb-4">'; // Maintain column structure
            echo '<div class="card h-100" style="background-color: #f0f0f0;">'; // Grey background for visual distinction
            echo '<div class="d-flex flex-column justify-content-center align-items-center" style="height: 100%;">';
            echo '<button class="btn btn-plus" onclick="ordertable()" style="width: 100%; height: 100%; font-size: 2rem;"><i class="fa fa-plus" aria-hidden="true"></i></button>'; // Plus button
            echo '<h5 class="card-title mt-2">Add Foods</h5>'; // Optional title for context
            echo '</div>';
            echo '</div>';
            echo '</div>';  // Close "Add Foods" card column

            echo '</div>'; // Close category group row
        }

        echo '</div>'; // Close main container for dynamic content

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
        <form action="User-panel.php" method="post" class="ml-2">
            <button type="submit" class="btn proceed-button" >Complete</button>
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

    // Check if quantity goes to zero
    if (currentQuantity < 1) {
        showAlertlist("Quantity can't be zero. Please press delete to remove the item.", 'error');
        return; // Exit the function without making an AJAX request
    }

    const newTotalPrice = (currentQuantity * unitPrice).toFixed(2);
    quantityElem.textContent = currentQuantity;
    priceElem.textContent = newTotalPrice;

    // AJAX request to update the quantity in the database
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

// Store the order_item_id in the modal's data attribute and show the modal
function openDeleteConfirmation(orderItemId) {
    const confirmModal = document.getElementById('confirmDeleteModal');
    confirmModal.setAttribute('data-order-item-id', orderItemId); // Set order_item_id in modal data attribute
    $('#confirmDeleteModal').modal('show'); // Show the confirmation modal
}

// Confirm and delete the item
function confirmDeleteItem() {
    const confirmModal = document.getElementById('confirmDeleteModal');
    const orderItemId = confirmModal.getAttribute('data-order-item-id'); // Retrieve order_item_id from modal data attribute

    if (!orderItemId) {
        showAlertlist("No item selected for deletion.", "error");
        return;
    }

    fetch('/Usercontrol/DeleteOrder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_item_id: orderItemId }) // Send order_item_id in request
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlertlist(data.message, 'success');
            // Remove the deleted item from the DOM using the unique ID
            const itemElement = document.getElementById(`order-item-${orderItemId}`);
            if (itemElement) itemElement.remove(); // Remove item from DOM
            $('#confirmDeleteModal').modal('hide'); // Hide modal after deletion
            Refresh_list(); // Refresh or update order list if needed
            
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
