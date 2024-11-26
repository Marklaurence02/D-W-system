<?php
session_name("user_session");
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the user_id and username from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

include_once "../assets/config.php"; // Ensure correct path to your config file
?>

<style>
    .menu-nav {
        margin: 5px;
        padding: 10px 20px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .menu-nav.active {
        background-color: #007bff;
        color: white;
    }

    .menu-nav:hover {
        background-color: #0056b3;
        color: white;
    }

    #searchInput {
        width: 50%;
        margin: 0 auto;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .food-box {
        margin-bottom: 20px;
        transition: transform 0.3s;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .food-box:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .food-btn {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .food-btn img {
        max-width: 100%;
        max-height: 150px;
        height: auto;
        transition: transform 0.3s;
        object-fit: contain;
    }

    .food-btn:hover img {
        transform: scale(1.1);
    }

    .food-box h6 {
        margin-top: 10px;
        text-align: center;
    }
</style>

<div class="container p-5"> 
    <h4 class="text-center">Pick Food</h4>
    <div class="menu_nav text-center mb-3">
        <?php
        // Fetch categories from the database
        $categoryQuery = "SELECT category_id, category_name FROM product_categories";
        $categoryResult = $conn->query($categoryQuery);

        if ($categoryResult->num_rows > 0) {
            while ($category = $categoryResult->fetch_assoc()) {
                echo '<button class="menu-nav" onclick="filterProducts(' . $category['category_id'] . ', this)">' . htmlspecialchars($category['category_name']) . '</button>';
            }
        } else {
            echo "<p>No categories available</p>";
        }
        ?>
    </div>

    <!-- Add a search input field -->
    <div class="text-center mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search for food..." onkeyup="searchProducts()">
    </div>

    <h1 class="text-center">Delicious Starts Here!</h1>

    <!-- Food Selection Section -->
    <div class="food-selection-container text-center row" id="foodSelectionContainer">
    <!-- Placeholder for the no products message -->
    <p id="noProductsMessage" class="w-100 text-center mt-4" style="display: none;">No Foods Available</p>

    <?php
    // Fetch all products initially
    $query = "SELECT product_id, product_name, price, special_instructions, product_image, category_id FROM product_items";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="food-box col-md-3 product-item" data-category="<?php echo $row['category_id']; ?>">
                <button class="food-btn" data-toggle="modal" data-target="#productModal<?php echo $row['product_id']; ?>">
                    <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                </button>
                <!-- Product name at the bottom -->
                <h6 class="mt-2"><?php echo htmlspecialchars($row['product_name']); ?></h6>
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
            <div class="modal-body d-flex align-items-center justify-content-center" id="tableAlertModalMessage">
                <!-- Success or error message will be displayed here with icon -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



            <!-- Modal for Product Details -->
            <div class="modal fade" id="productModal<?php echo $row['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="productModalLabel<?php echo $row['product_id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body d-flex">
                            <!-- Left side: Product Image -->
                            <div class="col-md-6">
                                <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" class="img-fluid rounded">
                            </div>
                            <!-- Right side: Product Details -->
                            <div class="col-md-6">
                                <p><strong>Price per Item:</strong> &#x20B1; <span id="pricePerItem<?php echo $row['product_id']; ?>"><?php echo number_format($row['price'], 2); ?></span></p>
                                <p><strong>Total Price:</strong> &#x20B1; <span id="totalPrice<?php echo $row['product_id']; ?>"><?php echo number_format($row['price'], 2); ?></span></p>
                                <p><strong>Instructions:</strong> <?php echo htmlspecialchars($row['special_instructions']); ?></p>
                                <div class="form-group">
                                    <label for="quantity<?php echo $row['product_id']; ?>">Quantity:</label>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button type="button" class="btn btn-qtyminus" onclick="changedQuantity(<?php echo $row['product_id']; ?>, -1)">
                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                        </button>
                                        <input type="number" id="quantity<?php echo $row['product_id']; ?>" name="quantity" min="1" value="1" class="form-control text-center mx-2" onchange="updateTotalPrice(<?php echo $row['product_id']; ?>)">
                                        <button type="button" class="btn btn-qtyplus" onclick="changedQuantity(<?php echo $row['product_id']; ?>, 1)">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <button class="btn btn-primary" onclick="confirmOrder(<?php echo $row['product_id']; ?>)">Confirm Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p class='text-center'>No food items available</p>";
    }
    ?>
</div>


<div class="container mt-4">
    <div class="d-flex justify-content-end">
        <form action="User-panel.php" method="post">
            <button type="submit" onclick="updateProgress();" class="btn proceed-button">Home</button>
            
        </form>       
        <button class="btn proceed-button ml-2" onclick="order_list()">Proceed</button> <!-- Add ml-2 for spacing -->
    </div>
</div>


<script>
     let selectedCategory = null;

     function updateTotalPrice(productId) {
    const quantity = document.getElementById(`quantity${productId}`).value;
    const pricePerItem = parseFloat(document.getElementById(`pricePerItem${productId}`).innerText);
    const totalPrice = pricePerItem * quantity;
    document.getElementById(`totalPrice${productId}`).innerText = totalPrice.toFixed(2);
}
function confirmOrder(productId) {
    const quantity = document.getElementById(`quantity${productId}`).value;
    const totalPrice = document.getElementById(`totalPrice${productId}`).innerText;

    // Send an AJAX request to save the order
    $.ajax({
        url: "/Usercontrol/saveOrder.php",
        method: "POST",
        data: {
            product_id: productId,
            quantity: quantity,
            price: parseFloat(totalPrice),
            user_id: <?php echo json_encode($user_id); ?>
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'success' || data.status === 'add') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    showConfirmButton: true
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    showConfirmButton: true
                });
            }

            // Close the product modal after order is confirmed
            $(`#productModal${productId}`).modal('hide');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error processing your order.',
                showConfirmButton: true
            });
        }
    });
}

function changedQuantity(productId, amount) {
    const quantityInput = document.getElementById(`quantity${productId}`);
    let currentQuantity = parseInt(quantityInput.value);
    currentQuantity += amount;
    if (currentQuantity < 1) currentQuantity = 1; // Ensure minimum quantity of 1
    quantityInput.value = currentQuantity;
    updateTotalPrice(productId); // Update the total price after adjustment
}
function filterProducts(categoryId, button) {
    const products = document.querySelectorAll('.product-item');
    const noProductsMessage = document.getElementById('noProductsMessage');
    const buttons = document.querySelectorAll('.menu-nav');

    // Toggle the active state of the button
    if (button.classList.contains('active')) {
        button.classList.remove('active');
        selectedCategory = null; // Show all products
    } else {
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        selectedCategory = categoryId; // Set selected category
    }

    let hasVisibleProducts = false;

    // Show or hide products based on selected category
    products.forEach(product => {
        const productCategory = product.getAttribute('data-category');
        if (selectedCategory === null || productCategory == selectedCategory) {
            product.style.display = 'block';
            hasVisibleProducts = true; // At least one product is visible
        } else {
            product.style.display = 'none';
        }
    });

    // Show or hide the "No products available" message
    if (hasVisibleProducts) {
        noProductsMessage.style.display = 'none';
    } else {
        noProductsMessage.style.display = 'block';
    }
}

function showAlertModal(message, type = 'success') {
    let iconType;

    // Define icon based on the type of message
    if (type === 'error') {
        iconType = 'error';
    } else if (type === 'add' || type === 'update') {
        iconType = 'info';
    } else {
        iconType = 'success';
    }

    // Use SweetAlert2 to show the alert
    Swal.fire({
        icon: iconType,
        title: message,
        showConfirmButton: true,
        timer: 3000
    });
}


// Example usage in AJAX success/error callbacks
function confirmOrder(productId) {
    const quantity = document.getElementById(`quantity${productId}`).value;
    const totalPrice = document.getElementById(`totalPrice${productId}`).innerText;

    // Send an AJAX request to save the order
    $.ajax({
        url: "/Usercontrol/saveOrder.php",
        method: "POST",
        data: {
            product_id: productId,
            quantity: quantity,
            price: parseFloat(totalPrice),
            user_id: <?php echo json_encode($user_id); ?>
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'success' || data.status === 'add') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    showConfirmButton: true
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    showConfirmButton: true
                });
            }

            // Close the product modal after order is confirmed
            $(`#productModal${productId}`).modal('hide');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error processing your order.',
                showConfirmButton: true
            });
        }
    });
}

function searchProducts() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const products = document.querySelectorAll('.product-item');
    let hasVisibleProducts = false;

    products.forEach(product => {
        const productName = product.querySelector('h6').innerText.toLowerCase();
        if (productName.includes(filter)) {
            product.style.display = 'block';
            hasVisibleProducts = true;
        } else {
            product.style.display = 'none';
        }
    });

    // Show or hide the "No products available" message
    const noProductsMessage = document.getElementById('noProductsMessage');
    if (hasVisibleProducts) {
        noProductsMessage.style.display = 'none';
    } else {
        noProductsMessage.style.display = 'block';
    }
}




  
   

</script>