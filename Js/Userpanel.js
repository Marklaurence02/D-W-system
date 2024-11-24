// Reusable function for AJAX requests
function loadContent(url, data = { record: 1 }, container = '.allContent-section') {
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
            $(container).html(response);
        },
        error: function(xhr, status, error) {
            console.error(`Error loading ${url}:`, error);
            alert(`Failed to load content from ${url}. Please try again.`);
        }
    });
}

// Specific functions using the reusable function
function ordertable() {
    loadContent('userview/ordertable.php');
}

function messageview() {
    loadContent('userview/message.php');
}

function paymentlist() {
    loadContent('userview/payment_list.php');
}

function recieptrecords() {
    loadContent('userview/reciept.php');
}

function reschedule() {
    loadContent('userview/reschedule.php');
}

function Feedback() {
    loadContent('userview/Feedback.php');
}

function reservetable() {
    loadContent('userview/tablereservation.php');
}

function savedreservation() {
    loadContent('userview/savedreservation.php');
}

function order_list() {
    loadContent('userview/order-list.php');
}

function Refresh_list() {
    loadContent('userview/order-list.php', { record: true }, '#orderContainer');
}

// Submit form with serialized data
function submitReservationForm() {
    const form = $('#reservationForm');
    loadContent('userview/tablereservation.php', form.serialize());
}

// Modal message handler for order list
function showOrderListModal(message, isError = false) {
    const messageElement = $('#orderListModalMessage');
    messageElement.removeClass('alert-success alert-danger').html('');

    if (isError) {
        messageElement.addClass('alert alert-danger').html(
            `<i class="fa fa-exclamation-triangle mr-2"></i> ${message}`
        );
    } else {
        messageElement.addClass('alert alert-success').html(
            `<i class="fa fa-check-circle mr-2"></i> ${message}`
        );
    }

    $('#orderListModal').modal('show');
}
