<?php
session_name("Staff_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div>
  <h2>All Customers</h2>

  <!-- Search and Date Filter Form -->
  <form id="filterForm" class="mb-3">
    <div class="row">
      <!-- Search Input -->
      <div class="col-12 col-md-4 mb-2 mb-md-0">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by username, email, or contact number">
      </div>
      
      <!-- Start Date -->
      <div class="col-12 col-md-3 mb-2 mb-md-0">
        <input type="date" id="startDate" class="form-control" placeholder="Start Date">
      </div>

      <!-- End Date -->
      <div class="col-12 col-md-3 mb-2 mb-md-0">
        <input type="date" id="endDate" class="form-control" placeholder="End Date">
      </div>

      <!-- Filter and Clear Buttons -->
      <div class="col-12 col-md-2 mb-2 mb-md-0">
        <button type="button" class="btn btn-primary w-100" onclick="UfilterItems()">Filter</button>
        <button type="button" class="btn btn-secondary w-100 mt-2 mt-md-0" onclick="clearFilters()">Clear</button>
      </div>
    </div>
  </form>

  <!-- Table for displaying users -->
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="text-center">S.N.</th>
          <th class="text-center">Username</th>
          <th class="text-center">Email</th>
          <th class="text-center">Contact Number</th>
          <th class="text-center">Joining Date</th>
        </tr>
      </thead>
      <tbody id="usersTableBody">
        <!-- Table rows will be populated here dynamically via AJAX -->
      </tbody>
    </table>
  </div>
</div>
