
<div>
  <h2>All General Users Report</h2>

  <!-- Users Table -->
  <div class="table-responsive">
    <table id="generalUsersTable" class="table table-bordered">
      <thead class="thead">
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
        dom: 'Bfrtip', // Add buttons to the table
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Copy',
                className: 'btn btn-info'
            },
            {
                extend: 'excelHtml5',
                text: 'Export to Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: 'Generate PDF',
                className: 'btn btn-danger',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'General Users Report'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-warning',
                title: 'All General Users Report',
                customize: function (win) {
                    $(win.document.body).css('font-size', '10pt')
                        .prepend('<h3>All General Users</h3>');
                }
            }
        ]
    });
});
</script>
