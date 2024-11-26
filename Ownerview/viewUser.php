<div class="container-fluid p-3">
    <div class="col-12">
        <div class="text-center mb-3">
            <h2>All General Users Report</h2>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table id="generalUsersTable" class="table table-striped table-hover table-bordered display nowrap">
                <thead>
                    <tr>
                        <th class="text-center">O.N.</th>
                        <th class="text-center">Username</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Contact Number</th>
                        <th class="text-center">Joining Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#generalUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/Ocontrols/loadAllUsers.php',
            type: 'POST',
        },
        columns: [
            { data: 'sn', className: 'text-center', orderable: false },
            { data: 'username', className: 'text-center' },
            { data: 'email', className: 'text-center' },
            { data: 'contact_number', className: 'text-center' },
            { data: 'created_at', className: 'text-center' }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[4, 'desc']],
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users..."
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'General Users Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'General Users Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'General Users Report',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-primary',
                titleAttr: 'Print Table',
                title: 'General Users Report',
                autoPrint: true
            }
        ],
        responsive: true,
        autoWidth: false
    });
});
</script>

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

/* Table Styling */
#generalUsersTable {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

#generalUsersTable th {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    border: none;
}

#generalUsersTable td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

#generalUsersTable tbody tr:hover {
    background-color: #f8f9fa;
}

/* Export Buttons */
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

/* Responsive styles */
@media (max-width: 768px) {
    .dataTables_filter input {
        width: 200px !important;
    }
    
    .dt-buttons {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .dt-button {
        width: 100%;
        margin-bottom: 5px;
    }
}
</style>
