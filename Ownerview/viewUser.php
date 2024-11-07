<?php
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
      <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by username, email, or contact number">
      </div>
      
      <!-- Start Date -->
      <div class="col-md-3">
        <input type="date" id="startDate" class="form-control" placeholder="Start Date">
      </div>

      <!-- End Date -->
      <div class="col-md-3">
        <input type="date" id="endDate" class="form-control" placeholder="End Date">
      </div>

      <!-- Filter and Clear Buttons -->
      <div class="col-md-2">
        <button type="button" class="btn btn-primary" onclick="UfilterItems()">Filter</button>
        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
      </div>
    </div>
  </form>

  <!-- Table for displaying users -->
  <table class="table">
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
