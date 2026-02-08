<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // Fetch filenames to delete physical files too (optional)
    $fileQuery = "SELECT identity_proof_filename, rent_agreement FROM rental_details WHERE id = $delete_id";
    $fileResult = mysqli_query($connect, $fileQuery);
    $fileRow = mysqli_fetch_assoc($fileResult);

    if ($fileRow) {
        if (!empty($fileRow['identity_proof_filename']) && file_exists($fileRow['identity_proof_filename'])) {
            unlink($fileRow['identity_proof_filename']);
        }
        if (!empty($fileRow['rent_agreement']) && file_exists($fileRow['rent_agreement'])) {
            unlink($fileRow['rent_agreement']);
        }
    }

    // Delete from database
    $deleteQuery = "DELETE FROM rental_details WHERE id = $delete_id";
    mysqli_query($connect, $deleteQuery);

    // Refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT * FROM rental_details";
$result = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SDS | Rental Records</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png">
    <link rel="stylesheet" href="style.css">
  <style>
 body {
    padding-top: 90px; /* Extra space for desktop fixed navbar */
  }

  @media (max-width: 768px) {
    body {
      padding-top: 100px; /* Extra space for mobile expanded navbar */
    }
  }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="SDS Logo.png" alt="Society Dashboard" height="40" class="me-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button> 
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ms-auto d-flex gap-2">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="maintenance.php"><i class="bi bi-tools me-1"></i>Maintenance</a></li>
            <li class="nav-item"><a class="nav-link" href="Religious.php"><i class="bi bi-flower1 me-1"></i>Religious Fund</a></li>
            <li class="nav-item"><a class="nav-link" href="balance.php"><i class="bi bi-cash-stack me-1"></i>Balance</a></li>
            <li class="nav-item"><a class="nav-link" href="Receipt.php"><i class="bi bi-receipt-cutoff me-1"></i>Receipts</a></li>
            <li class="nav-item"><a class="nav-link" href="Bankdebit.php"><i class="bi bi-bank me-1"></i>Bank</a></li>
            <li class="nav-item"><a class="nav-link" href="housedetails.php"><i class="bi bi-building me-1"></i>House Details</a></li>
            <li class="nav-item"><a class="nav-link" href="adminrental.php"><i class="bi bi-house me-1"></i>Rental Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>


<!-- Content -->
<div class="container mt-5">
  <div class="table-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0"><i class="bi bi-house-fill me-2"></i>Tenant Rental Details</h4>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover table-striped align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>House No</th>
            <th>Owner Name</th>
            <th>Tenant Name</th>
            <th>Adults</th>
            <th>Children</th>
            <th>Family</th>
            <th>2W</th>
            <th>4W</th>
            <th>Total Vehicles</th>
            <th>Contact 1</th>
            <th>Contact 2</th>
            <th>ID Proof</th>
            <th>Agreement</th>
            <th></th>

          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td><?= htmlspecialchars($row['house_no']) ?></td>
    <td><?= htmlspecialchars($row['owner_name']) ?></td>
    <td><?= htmlspecialchars($row['tenant_name']) ?></td>
    <td><?= $row['adults'] ?></td>
    <td><?= $row['children'] ?></td>
    <td><?= $row['family_members'] ?></td>
    <td><?= $row['two_wheeler'] ?></td>
    <td><?= $row['four_wheeler'] ?></td>
    <td><?= $row['vehicles'] ?></td>
    <td><?= htmlspecialchars($row['contact_number_1']) ?></td>
    <td><?= htmlspecialchars($row['contact_number_2']) ?></td>
    <td>
      <?php if (!empty($row['identity_proof_filename'])): ?>
        <a href="<?= $row['identity_proof_filename'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
      <?php else: ?>N/A<?php endif; ?>
    </td>
    <td>
      <?php if (!empty($row['rent_agreement'])): ?>
        <a href="<?= $row['rent_agreement'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
      <?php else: ?>N/A<?php endif; ?>
    </td>
    <td>
      
    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">
    <i class="bi bi-trash me-1"></i> Delete 
  </button>

  <script>
function confirmDelete(id) {
  document.getElementById('deleteNoticeId').value = id;
  new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}
</script>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">
          <i class="bi bi-exclamation-circle"></i> Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this details?
      </div>
      <div class="modal-footer">
        <form method="POST" action="">
        <input type="hidden" name="delete_id" id="deleteNoticeId">
        <input type="hidden" name="delete" value="1">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
      </form>
    </td>
  </tr>
<?php endwhile; ?>

        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($connect); ?>
