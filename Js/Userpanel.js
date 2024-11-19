


function ordertable() {  
    $.ajax({
        url: "userview/ordertable.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}


function messageview(){
    $.ajax({
        url: "userview/message.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}


function paymentlist() {  
    $.ajax({
        url: "userview/payment_list.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function recieptrecords() {  
    $.ajax({
        url: "userview/reciept.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}


function reservetable() {  
    $.ajax({
        url: "userview/tablereservation.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function savedreservation() {  
    $.ajax({
        url: "userview/savedreservation.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

// Order list
// Function to display messages in the modal
function showOrderListModal(message, isError = false) {
    const messageElement = document.getElementById('orderListModalMessage');
    
    messageElement.innerHTML = ''; // Clear previous content
    messageElement.classList.remove('alert-success', 'alert-danger'); // Clear previous classes

    if (isError) {
        messageElement.classList.add('alert', 'alert-danger');
        messageElement.innerHTML = `<i class="fa fa-exclamation-triangle mr-2" aria-hidden="true"></i> ${message}`;
    } else {
        messageElement.classList.add('alert', 'alert-success');
        messageElement.innerHTML = `<i class="fa fa-check-circle mr-2" aria-hidden="true"></i> ${message}`;
    }

    $('#orderListModal').modal('show'); // Display the alert modal
}


// Quantity for orderlist









function order_list() {  
    $.ajax({
        url: "userview/order-list.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data); // Update the section with the new order list
        }
    });
}

function Refresh_list() {
    $.ajax({
        url: 'userview/order-list.php', // Path to PHP file generating the order list
        type: 'POST',
        data: { record: true }, // Send necessary data if required
        success: function(response) {
            // Replace only the content of the #orderContainer div with the response
            $('#orderContainer').html(response);
        },
        error: function() {
            console.log("An error occurred while refreshing the order list.");
        }
    });
}