<?php
$conn = new mysqli("localhost", "root", "", "sds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$receipt_no = $_POST['receipt_no'] ?? '';

$name = $m_date = $house_no = "N/A";
$maintenance = $development = $transfer = $penalty = $other = $rebate = 0;

if (!empty($receipt_no)) {
    $stmt = $conn->prepare("SELECT house_no, m_date FROM maintenance WHERE receipt_no = ?");
    $stmt->bind_param("s", $receipt_no);
    $stmt->execute();
    $stmt->bind_result($house_no, $m_date);
    $stmt->fetch();
    $stmt->close();

    $stmt2 = $conn->prepare("SELECT name FROM house_plot WHERE house_no = ?");
    $stmt2->bind_param("s", $house_no);
    $stmt2->execute();
    $stmt2->bind_result($name);
    $stmt2->fetch();
    $stmt2->close();

    $types = [
        'Maintenance' => 'maintenance',
        'Development' => 'development',
        'Transferfee' => 'transfer',
        'Penalty' => 'penalty',
        'Otherrent' => 'other',
        'Rebate' => 'rebate'
    ];

    foreach ($types as $type => $varName) {
        $stmt = $conn->prepare("SELECT SUM(ammount) FROM maintenance WHERE receipt_no = ? AND paymenttype = ?");
        $stmt->bind_param("ss", $receipt_no, $type);
        $stmt->execute();
        $stmt->bind_result($$varName);
        $stmt->fetch();
        $stmt->close();
        $$varName = $$varName ?? 0;
    }

    $total = $maintenance + $development + $transfer + $penalty + $other - $rebate;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="home.png"> 

    <style>
    @page {
        size: A4 landscape;
        margin: 15mm;
    }

    @media print {
        html, body {
            width: 297mm;
            height: 210mm;
        }

        body * {
            visibility: hidden;
        }

        #receiptToPrint, #receiptToPrint * {
            visibility: visible;
        }

        #receiptToPrint {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .btn-container, .form-box {
            display: none !important;
        }

        .receipt {
            opacity: 0.95;
            box-shadow: none !important;
            border: 2px solid red !important;
            padding: 10mm !important;
            margin: 0 auto !important;
            width: 100% !important;
            background-color: #fff !important;
            page-break-inside: avoid;
        }

        .table td, .table th {
            padding: 6px !important;
            font-size: 14px !important;
        }
    }

    .receipt {
        font-family: Arial, sans-serif;
        border: 2px solid red;
        padding: 30px;
        margin: 20px auto;
        max-width: 1000px;
        background-color: #fff;
        position: relative;
        opacity: 0.95;
    }

    .receipt h1 {
        color: red;
        text-align: center;
    }

    .receipt p, .receipt td {
        font-size: 16px;
    }

    .society-info {
        text-align: center;
        margin: 0;
        font-size: 15px;
    }

    table.table {
        border: 2px solid #000;
        margin-top: 15px;
    }

    table.table thead {
        background-color: #343a40;
        color: #fff;
    }

    table.table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    th, td {
        border: 1px solid #000;
        padding: 10px;
        text-align: left;
    }

    .footer {
        font-size: 15px;
        margin-top: 20px;
    }
</style>


</head>
<body class="bg-light">

<div class="container py-4">
    <div class="form-box mb-4">
        <form method="POST" class="d-flex flex-column flex-md-row gap-2 justify-content-center">
            <input type="text" name="receipt_no" placeholder="Enter Receipt No" required class="form-control w-auto">
            <button type="submit" class="btn btn-primary">Generate Receipt</button>
            <button onclick="history.back()" type="button" class="btn btn-outline-secondary">Back</button>

        </form>
    </div>

    <?php if (!empty($receipt_no)) : ?>
    <div id="receiptToPrint">
        <div class="receipt shadow">
            <div class="d-flex align-items-center mb-3">
                <img src="receiptlogo.jpg" alt="Society Logo" height="80" class="me-3">
                <div class="flex-grow-1 text-center">
                    <p class="society-info">Shree Ganeshay Namah</p>
                    <h1><u>SDS Row House</u></h1>
                    <p class="society-info">T.P.Scheme-44, Opp. Ganganagar Society, Beside CNG Pump,<br> Jahangirabad, Dandi Road, Surat-395005</p>
                    <p class="society-info">Contact: +91-9805547446 | +91-9427152509</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-6">House No: <strong><?= $house_no ?></strong></div>
                <div class="col-6 text-end">Date: <strong><?= $m_date ?></strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-6">Name: <strong><?= $name ?></strong></div>
                <div class="col-6 text-end">Receipt No: <strong><?= $receipt_no ?></strong></div>
            </div>

            <p>We have received the following payments for society:</p>
            <table class="table table-bordered">
                <tr><th>Sr No.</th><th>Description</th><th>Amount (₹)</th></tr>
                <tr><td>1</td><td>Maintenance</td><td>₹<?= number_format($maintenance, 2) ?></td></tr>
                <tr><td>2</td><td>Development</td><td>₹<?= number_format($development, 2) ?></td></tr>
                <tr><td>3</td><td>Transfer Fee</td><td>₹<?= number_format($transfer, 2) ?></td></tr>
                <tr><td>4</td><td>Penalty</td><td>₹<?= number_format($penalty, 2) ?></td></tr>
                <tr><td>5</td><td>Other</td><td>₹<?= number_format($other, 2) ?></td></tr>
                <tr><td>6</td><td>Rebate</td><td>-₹<?= number_format($rebate, 2) ?></td></tr>
                <tr><td colspan="2"><strong>Total</strong></td><td><strong>₹<?= number_format($total, 2) ?></strong></td></tr>
            </table>

            <div class="footer mt-4">
                <div class="row">
                    <div class="col-md-6">Cheque No: ________________</div>
                    <div class="col-md-6 text-end">Date: ________________</div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">Signature of Payer: <strong><?= $name ?></strong></div>
                    <div class="col-md-6 text-end">Receiver: __________________</div>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-container text-center mt-3">
    <button onclick="window.print()" class="btn btn-success me-2">
        <i class="bi bi-printer"></i> Print Receipt
    </button>
    <button onclick="window.location.reload()" class="btn btn-secondary me-2">
        <i class="bi bi-arrow-clockwise"></i> New Receipt
    </button>
    <button onclick="shareReceipt()" class="btn btn-warning">
        <i class="bi bi-share-fill"></i> Share Receipt
    </button>
</div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function shareReceipt() {
    const receiptText = `
Receipt No: <?= $receipt_no ?>  
Name: <?= $name ?>  
House No: <?= $house_no ?>  
Date: <?= $m_date ?>  
Total: ₹<?= number_format($total, 2) ?>  
Thank you.
`;

    if (navigator.share) {
        navigator.share({
            title: 'Shivdrashti Receipt',
            text: receiptText,
        }).catch((err) => {
            alert("Share cancelled or failed.");
        });
    } else {
        const whatsappURL = `https://wa.me/?text=${encodeURIComponent(receiptText)}`;
        window.open(whatsappURL, '_blank');
    }
}
</script>
</body>
</html>
