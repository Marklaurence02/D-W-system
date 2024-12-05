<?php
session_start();
include_once "../assets/config.php";

// Handle form submission to update reservation status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reservation_id'], $_POST['status'])) {
    $reservationId = intval($_POST['reservation_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE reservations SET status = ?, updated_at = NOW() WHERE reservation_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $reservationId);
        $response = $stmt->execute() 
            ? ['status' => 'success', 'message' => 'Reservation status updated successfully.']
            : ['status' => 'error', 'message' => 'Error updating reservation status: ' . $stmt->error];
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'SQL error: ' . $conn->error];
    }

    if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
        echo json_encode($response);
        exit;
    }
    exit(json_encode($response));
}

// Modify the PHP section to return JSON when requested
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['ajax'])) {
    $sql = "SELECT r.reservation_id, r.status, r.reservation_date, r.reservation_time, 
                   t.table_number, t.seating_capacity, 
                   CONCAT(u.first_name, ' ', u.last_name) AS reserved_by
            FROM reservations r
            LEFT JOIN tables t ON r.table_id = t.table_id
            LEFT JOIN users u ON r.user_id = u.user_id";
    
    $result = $conn->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $statusInfo = match($row['status']) {
            'Pending' => ['class' => 'status-pending', 'icon' => 'fa-clock'],
            'Complete' => ['class' => 'status-completed', 'icon' => 'fa-check-circle'],
            'Canceled' => ['class' => 'status-canceled', 'icon' => 'fa-times-circle'],
            'Rescheduled' => ['class' => 'status-progress', 'icon' => 'fa-sync'],
            'Reserved' => ['class' => 'status-completed', 'icon' => 'fa-check-double'],
            default => ['class' => 'status-default', 'icon' => 'fa-question-circle']
        };
        
        $statusBadge = "<span class='status-badge {$statusInfo['class']}'>
                           <i class='fas {$statusInfo['icon']}'></i>
                           {$row['status']}
                       </span>";
        
        $actions = "<form method='POST' class='reservation-form'>
                       <input type='hidden' name='reservation_id' value='{$row['reservation_id']}'>
                       <select name='status' class='form-select status-select mb-2'>";
        
        foreach (['Pending', 'Complete', 'Canceled', 'Rescheduled', 'Reserved'] as $status) {
            $selected = $row['status'] === $status ? 'selected' : '';
            $actions .= "<option value='$status' $selected>$status</option>";
        }
        
        $actions .= "</select>
                    <button type='submit' class='btn-view-details'>
                        <i class='fas fa-save'></i> Update
                    </button>
                   </form>";
        
        $data[] = [
            'reservation_id' => $row['reservation_id'],
            'reserved_by' => $row['reserved_by'],
            'date' => date("F j, Y", strtotime($row['reservation_date'])),
            'time' => date("g:i A", strtotime($row['reservation_time'])),
            'table_number' => $row['table_number'],
            'seating_capacity' => $row['seating_capacity'],
            'status' => $statusBadge,
            'actions' => $actions
        ];
    }
    
    echo json_encode(['data' => $data]);
    exit;
}

// Fetch reservations
$sql = "SELECT r.reservation_id, r.status, r.reservation_date, r.reservation_time, 
               t.table_number, t.seating_capacity, 
               CONCAT(u.first_name, ' ', u.last_name) AS reserved_by
        FROM reservations r
        LEFT JOIN tables t ON r.table_id = t.table_id
        LEFT JOIN users u ON r.user_id = u.user_id";
$reservations = $conn->query($sql);

// Simplify the initial status counts - we'll update them via JavaScript
$statusCounts = array(
    'all' => 0,
    'Pending' => 0,
    'Complete' => 0,
    'Canceled' => 0,
    'Rescheduled' => 0,
    'Reserved' => 0
);
?>

<!-- Include required CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet">

<!-- Main Content -->
<div class="container-fluid p-3">
    <div class="col-12">
        <div class="text-center mb-3">
            <h2>Reservation Details</h2>
        </div>

        <!-- Filter Buttons -->
        <div class="filter-container mb-4">
            <div class="status-filters">
                <button class="filter-btn active" data-status="all">
                    All Reservations
                </button>
                <button class="filter-btn" data-status="Pending">
                    Pending
                </button>
                <button class="filter-btn" data-status="Complete">
                    Complete
                </button>
                <button class="filter-btn" data-status="Canceled">
                    Canceled
                </button>
                <button class="filter-btn" data-status="Rescheduled">
                    Rescheduled
                </button>
                <button class="filter-btn" data-status="Reserved">
                Reserved
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="table-responsive">
            <table id="reservationTable" class="table table-striped table-hover table-bordered display nowrap">
                <thead>
                    <tr>
                        <th>R.N.</th>
                        <th>Reserved By</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Table</th>
                        <th>Capacity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reservations && $reservations->num_rows > 0): 
                        while ($row = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['reservation_id']) ?></td>
                                <td><?= htmlspecialchars($row['reserved_by']) ?></td>
                                <td><?= date("F j, Y", strtotime($row['reservation_date'])) ?></td>
                                <td><?= date("g:i A", strtotime($row['reservation_time'])) ?></td>
                                <td><?= htmlspecialchars($row['table_number']) ?></td>
                                <td><?= htmlspecialchars($row['seating_capacity']) ?></td>
                                <td class="text-center">
                                    <?php
                                    $statusInfo = match($row['status']) {
                                        'Pending' => [
                                            'class' => 'status-pending',
                                            'icon' => 'fa-clock',
                                            'text' => 'Pending'
                                        ],
                                        'Complete' => [
                                            'class' => 'status-completed',
                                            'icon' => 'fa-check-circle',
                                            'text' => 'Complete'
                                        ],
                                        'Canceled' => [
                                            'class' => 'status-canceled',
                                            'icon' => 'fa-times-circle',
                                            'text' => 'Canceled'
                                        ],
                                        'Rescheduled' => [
                                            'class' => 'status-progress',
                                            'icon' => 'fa-sync',
                                            'text' => 'Rescheduled'
                                        ],
                                        'Reserved' => [
                                            'class' => 'status-completed',
                                            'icon' => 'fa-check-double',
                                            'text' => 'Reserved'
                                        ],
                                        default => [
                                            'class' => 'status-default',
                                            'icon' => 'fa-question-circle',
                                            'text' => $row['status']
                                        ]
                                    };
                                    ?>
                                    <span class="status-badge <?= $statusInfo['class'] ?>">
                                        <i class="fas <?= $statusInfo['icon'] ?>"></i>
                                        <?= $statusInfo['text'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="8" class="text-center">No reservations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Your custom JS -->
<script>
// Initialize DataTable
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#reservationTable')) {
        $('#reservationTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    var table = $('#reservationTable').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",           // Removes the 'Search' label
            searchPlaceholder: "Search reservations..."  // Adds placeholder
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'Reservation Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'Reservation Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'Reservation Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-primary',
                titleAttr: 'Print Table',
                title: 'Reservation Report',
                autoPrint: true
            }
        ]
    });

    // Filter button click handler
    $('.filter-btn').on('click', function() {
        var status = $(this).data('status');
        
        // Remove active class from all buttons and add to clicked button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Apply the filter
        if (status === 'all') {
            table.column(6).search('').draw();
        } else {
            table.column(6).search(status).draw();
        }
    });

    // Handle form submission
    $('.reservation-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $statusCell = $form.closest('tr').find('td:nth-child(7)'); // Status is in 7th column
        var newStatus = $form.find('select[name="status"]').val();
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm Status Change',
            text: `Are you sure you want to change the status to "${newStatus}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FD6610',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with AJAX call
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize() + '&ajax=1',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            var statusInfo = getStatusInfo(newStatus);
                            var statusBadge = `<span class="status-badge ${statusInfo.class}">
                                <i class="fas ${statusInfo.icon}"></i>
                                ${newStatus}
                            </span>`;
                            $statusCell.html(statusBadge);
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Updated',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating status: ' + error,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            } else {
                // Reset select to previous value if user cancels
                $form.find('select[name="status"]').val($statusCell.find('.status-badge').text().trim());
            }
        });
    });

    // Helper function to get status info
    function getStatusInfo(status) {
        switch(status) {
            case 'Pending':
                return { class: 'status-pending', icon: 'fa-clock' };
            case 'Complete':
                return { class: 'status-completed', icon: 'fa-check-circle' };
            case 'Canceled':
                return { class: 'status-canceled', icon: 'fa-times-circle' };
            case 'Rescheduled':
                return { class: 'status-progress', icon: 'fa-sync' };
            case 'Paid':
                return { class: 'status-completed', icon: 'fa-check-double' };
            default:
                return { class: 'status-default', icon: 'fa-question-circle' };
        }
    }
});
</script>

<!-- Your CSS -->
<style>
/* DataTable Container */
.dataTables_wrapper {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* Search Box Styling */
.dataTables_filter {
    position: relative;
    margin-bottom: 20px;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    margin: 0;
    width: 100%;
    position: relative;
}

.dataTables_filter input {
    width: 300px !important;
    height: 40px;
    padding: 8px 16px 8px 40px !important;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    margin: 0 !important;
}

/* Hide the "Search:" text */
.dataTables_filter label > span,
.dataTables_filter label::before {
    display: none;
}

/* Add search icon */
.dataTables_filter::before {
    content: "\f002";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    z-index: 1;
    pointer-events: none;
}

/* Focus state */
.dataTables_filter input:focus {
    outline: none;
    border-color: #FD6610;
    box-shadow: 0 0 0 3px rgba(253, 102, 16, 0.1);
}

/* Filter Styling */
.filter-container {
    margin: 20px 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 4px;
}

.status-filters {
    display: flex;
    gap: 10px;
    flex-wrap: nowrap;
    min-width: max-content;
}

.filter-btn {
    padding: 8px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    color: #666;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.filter-btn:hover {
    background-color: #f8f9fa;
    border-color: #FD6610;
    color: #FD6610;
}

.filter-btn.active {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(253, 102, 16, 0.2);
}

/* Status Badge Styling */
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 120px;
    justify-content: center;
}

.status-badge i {
    font-size: 14px;
}

/* Status Colors */
.status-pending {
    background-color: #fff5f5;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.status-progress {
    background-color: #fff8e1;
    color: #ffc107;
    border: 1px solid #ffc107;
}

.status-completed {
    background-color: #f0fff4;
    color: #28a745;
    border: 1px solid #28a745;
}

.status-canceled {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #6c757d;
}

/* Table Styling */
#reservationsTable {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

#reservationsTable th {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    border: none;
}

#reservationsTable td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

#reservationsTable tbody tr:hover {
    background-color: #f8f9fa;
}

/* View Details Button */
.btn-view-details {
    padding: 6px 14px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-view-details i {
    font-size: 14px;
}

.btn-view-details:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2);
    color: white;
}

/* Export Buttons Styling */
.dt-buttons {
    margin-bottom: 20px;
}

.dt-button {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    transition: all 0.3s ease !important;
    margin-right: 8px !important;
}

.dt-button:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2) !important;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dataTables_filter input {
        width: 200px !important;
    }
}

@media (max-width: 992px) {
    .filter-container {
        margin: 15px 0;
    }
    
    .filter-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .dataTables_wrapper {
        padding: 15px;
    }
    
    .dataTables_filter input {
        width: 150px !important;
    }
    
    .dt-buttons {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .dt-buttons .dt-button {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .status-badge {
        padding: 6px 12px;
        font-size: 12px;
        min-width: 100px;
    }
}

@media (max-width: 576px) {
    .filter-btn {
        padding: 4px 10px;
        font-size: 12px;
    }
}

/* Add these styles */
.status-select {
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid #e0e0e0;
    background-color: white;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 140px;
}

.status-select:focus {
    outline: none;
    border-color: #FD6610;
    box-shadow: 0 0 0 3px rgba(253, 102, 16, 0.1);
}
</style>
