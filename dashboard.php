<?php
// Connect to database
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $notice_id = $_POST['notice_id'];
    $new_title = mysqli_real_escape_string($connect, $_POST['new_title']);
    $new_content = mysqli_real_escape_string($connect, $_POST['new_content']);

    $update_query = "UPDATE notices SET title='$new_title', content='$new_content' WHERE id='$notice_id'";
    mysqli_query($connect, $update_query);
    header("Location: dashboard.php");
    exit;
}

// Fetch notices
$notices = mysqli_query($connect, "SELECT * FROM notices ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png"> 

    <style>
        body {
            background-image: url('SDSBG.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .notice-card {
            max-width: 600px;
            margin: 20px auto;
            background-color: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .edit-form {
            display: none;
            margin-top: 15px;
        }
        .custom-add-btn {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
            font-weight: bold;
            padding: 12px 25px;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            text-transform: uppercase;
        }
        .custom-add-btn:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
        }
        .nav-link:hover {
            font-weight: bold;
            color: #ffc107 !important;
        }
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
            <li class="nav-item"><a class="nav-link" href="religiousreport.php"><i class="bi bi-file-earmark-text me-1"></i>Religious Report</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Main Section -->
<div class="container mt-5">
    <h1 class="text-center fw-bold text-decoration-underline">SDS Row House</h1>
    <h2 class="text-center text-decoration-underline">Notice Board</h2>

    <?php while ($row = mysqli_fetch_assoc($notices)) : ?>
        <div class="notice-card">
            <h5 class="text-danger text-center fw-bold"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="text-center"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <p class="text-muted text-end mb-2">Posted on: <?= $row['date_posted'] ?></p>

            
            <!-- Edit Form -->
<form class="edit-form" id="edit-form-<?= $row['id'] ?>" method="POST">
  <input type="hidden" name="notice_id" value="<?= $row['id'] ?>">
  
  <div class="mb-2">
    <input type="text" name="new_title" class="form-control" value="<?= htmlspecialchars($row['title']) ?>" required>
  </div>
  
  <div class="mb-2">
    <textarea name="new_content" class="form-control" rows="3" required><?= htmlspecialchars($row['content']) ?></textarea>
  </div>

  <!-- Update + Cancel with Icons -->
  <div class="d-flex gap-2">
    <button type="submit" name="update" class="btn btn-primary">
      <i class="bi bi-check-circle me-1"></i> Update Notice
    </button>
    <button type="button" class="btn btn-secondary" onclick="toggleEditForm(<?= $row['id'] ?>)">
      <i class="bi bi-x-circle me-1"></i> Cancel
    </button>
  </div>
</form>

<!-- Main Edit/Delete Buttons -->
<div class="d-flex justify-content-between mt-3">
  <!-- Edit Button -->
  <button class="btn btn-warning" onclick="toggleEditForm(<?= $row['id'] ?>)">
    <i class="bi bi-pencil-square me-1"></i> Edit Notice
  </button>

  <!-- Delete Button (Triggers Modal) -->
  <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">
    <i class="bi bi-trash me-1"></i> Delete Notice
  </button>
</div>


          <!-- End of Notice Card -->
        </div>  
    <?php endwhile; ?>
</div>

<!-- Add Notice Button -->
<div class="text-center mt-4">
    <a href="add_notice.php" class="btn custom-add-btn">
        <i class="bi bi-plus-circle me-1"></i> Add New Notice
    </a>
</div>

<script>
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
        Are you sure you want to delete this notice?
      </div>
      <div class="modal-footer">
        <form method="POST" action="delete_notice.php">
          <input type="hidden" name="notice_id" id="deleteNoticeId">
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


</body>
</html>
