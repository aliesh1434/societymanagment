<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch house number
$query = "SELECT house_no FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$house_no = $user['house_no'] ?? '';
$stmt->close();

if (!$house_no) die("Error: No house number found for this user.");

// Fetch house owner name
$stmt = $connect->prepare("SELECT name FROM house_plot WHERE house_no = ?");
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
$house_data = $result->fetch_assoc();
$stmt->close();

$owner_name = $house_data['name'] ?? 'N/A';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_name = $_POST['tenant_name'];
    $adults = (int)$_POST['adults'];
    $children = (int)$_POST['children'];
    $family_members = $adults + $children;

    $two_wheeler = (int)$_POST['two_wheeler'];
    $four_wheeler = (int)$_POST['four_wheeler'];
    $vehicles = $two_wheeler + $four_wheeler;

    $contact1 = $_POST['contact1'];
    $contact2 = $_POST['contact2'];

    // Handle identity proof upload
    $uploadDir = "uploads/identity_proofs/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $identity_proof_filename = null;
    if (isset($_FILES['identity_proof']) && $_FILES['identity_proof']['error'] === 0) {
        if ($_FILES['identity_proof']['size'] <= 2 * 1024 * 1024) {
            $ext = pathinfo($_FILES['identity_proof']['name'], PATHINFO_EXTENSION);
            $identity_proof_filename = $uploadDir . 'proof_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            move_uploaded_file($_FILES['identity_proof']['tmp_name'], $identity_proof_filename);
        } else {
            $error = "Identity Proof file exceeds the 2MB size limit.";
        }
    } else {
        $error = "Failed to upload identity proof.";
    }

    // Handle rental agreement upload
    $rentAgreementFile = null;
if (isset($_FILES['rental_agreements']) && $_FILES['rental_agreements']['error'] === 0)
        if ($_FILES['rental_agreements']['size'] <= 2 * 1024 * 1024) {
            $uploadAgreementDir = "uploads/rental_agreements/";
            if (!is_dir($uploadAgreementDir)) mkdir($uploadAgreementDir, 0755, true);

            $ext = pathinfo($_FILES['rental_agreements']['name'], PATHINFO_EXTENSION);
            $rentAgreementFile = $uploadAgreementDir . 'agreement_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            move_uploaded_file($_FILES['rental_agreements']['tmp_name'], $rentAgreementFile);
        } else {
    $uploadError = $_FILES['rental_agreements']['error'];
    $error = "Failed to upload rental agreement. Error Code: $uploadError";
}


    if (!isset($error)) {
        $sql = "INSERT INTO rental_details
        (house_no, owner_name, tenant_name, family_members, adults, children,
        vehicles, two_wheeler, four_wheeler, contact_number_1, contact_number_2,
        identity_proof_filename, rent_agreement)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param(
            "sssiiiiiiisss",
            $house_no,
            $owner_name,
            $tenant_name,
            $family_members,
            $adults,
            $children,
            $vehicles,
            $two_wheeler,
            $four_wheeler,
            $contact1,
            $contact2,
            $identity_proof_filename,
            $rentAgreementFile
        );
    

        if ($stmt->execute()) {
            echo "<div class='login-feedback'>
                <div class='tick-box tick-success'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle' cx='26' cy='26' r='25'/>
                        <path class='tick-check' d='M14,27 L22,35 L38,19'/>
                    </svg>
                    <p class='tick-message text-success'>Rental Details added Successfully!</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'userdashboard.php';
                    }, 2500);
                </script>
            </div>";
        } else {
            $error = "Error saving data: " . $stmt->error;
        }

        $stmt->close();
    }
    }
mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SDS | Tenant Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="home.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="SDS Logo.jpg" alt="Logo" height="40"></a>
    <div class="ms-auto">
      <a class="btn btn-outline-light" href="Userdashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="btn btn-outline-light" href="Index.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container" style="margin-top: 80px;">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
      <h4>Enter House Tenant Details (If exists)</h4>
    </div>
    <div class="card-body">
      <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
        <div class="mb-3">
          <label class="form-label">House Number</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($house_no) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label">Owner Name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($owner_name) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label">Tenant Name</label>
          <input type="text" name="tenant_name" class="form-control" required>
            <div class="invalid-feedback">Please enter the tenant's name.</div>
        </div>

        <div class="mb-3">
  <label class="form-label">Family Members</label>
  <div class="row g-2">
    <div class="col-md-4">
      <label class="form-label">Adults</label>
      <input type="number" name="adults" id="adults" class="form-control" min="0"  required>
      <div class="invalid-feedback">Please enter number of adults.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label">Children</label>
      <input type="number" name="children" id="children" class="form-control" min="0"  required>
      <div class="invalid-feedback">Please enter number of children.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label">Total Members</label>
      <input type="number" name="family_members" id="total_members" class="form-control" readonly>
      <div class="invalid-feedback">Total will be calculated automatically.</div>
    </div>
  </div>
</div>


        <div class="mb-3">
  <label class="form-label">Vehicles</label>
  <div class="row g-2">
    <div class="col-md-4">
      <label class="form-label">2-Wheeler</label>
      <input type="number" name="two_wheeler" id="two_wheeler" class="form-control" min="0"  required>
      <div class="invalid-feedback">Please enter number of 2-wheelers.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label">4-Wheeler</label>
      <input type="number" name="four_wheeler" id="four_wheeler" class="form-control" min="0"  required>
      <div class="invalid-feedback">Please enter number of 4-wheelers.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label">Total Vehicles</label>
      <input type="number" name="vehicles" id="total_vehicles" class="form-control" readonly>
      <div class="invalid-feedback">Total will be auto-calculated.</div>
    </div>
  </div>
</div>


        <div class="mb-3">
          <label class="form-label">Tenant Contact Number 1</label>
          <input type="text" name="contact1" class="form-control" required>
          <div class="invalid-feedback">Please enter the primary contact number.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Tenant Contact Number 2</label>
          <input type="text" name="contact2" class="form-control" required>
          <div class="invalid-feedback">Please enter the secondary contact number.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Identity Proof <span class="text-muted">(e.g., Aadhar Card, PAN Card)</span></label>
          <input type="file" name="identity_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
          <div class="form-text">Upload JPG, PNG, or PDF only. Max size: 2MB</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Rental Agreement</label>
<input type="file" name="rental_agreements" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>

          <div class="form-text">Upload JPG, PNG, or PDF only. Max size: 2MB</div>
        </div>

        <button type="submit" class="btn btn-success">Save Details</button>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('identity_proof').addEventListener('change', function () {
    const file = this.files[0];
    if (file && file.size > 2 * 1024 * 1024) {
      alert("File size exceeds 2MB limit. Please upload a smaller file.");
      this.value = "";
    }
  });
</script>
<script>
  function updateTotalVehicles() {
    const two = parseInt(document.getElementById("two_wheeler").value) || 0;
    const four = parseInt(document.getElementById("four_wheeler").value) || 0;
    document.getElementById("total_vehicles").value = two + four;
  }

  document.getElementById("two_wheeler").addEventListener("input", updateTotalVehicles);
  document.getElementById("four_wheeler").addEventListener("input", updateTotalVehicles);

  // Trigger once on load in case default values are set
  updateTotalVehicles();
</script>
<script>
  function updateTotalMembers() {
    const adults = parseInt(document.getElementById("adults").value) || 0;
    const children = parseInt(document.getElementById("children").value) || 0;
    document.getElementById("total_members").value = adults + children;
  }

  document.getElementById("adults").addEventListener("input", updateTotalMembers);
  document.getElementById("children").addEventListener("input", updateTotalMembers);

  updateTotalMembers(); // Initial call
</script>
<script>
  document.querySelector("input[name='rental_agreements']").addEventListener('change', function () {
    const file = this.files[0];
    if (file && file.size > 2 * 1024 * 1024) {
      alert("Rental Agreement file size exceeds 2MB.");
      this.value = "";
    }
  });
</script>
<script>
// Bootstrap 5 client-side validation
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>


</body>
</html>