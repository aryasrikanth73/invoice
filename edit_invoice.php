<?php
session_start();
if(!isset($_SESSION['id'],$_SESSION['email'])){
    header('location:login.php');
}
// Database configuration
$host = 'localhost'; // or your database host
$db = 'invoice_system';
$user = 'root'; // or your database username
$pass = ''; // or your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch invoice details based on the provided ID
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Fetch invoice details
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch advance payments
        $stmt = $pdo->prepare("SELECT * FROM advance_payments WHERE invoice_id = ?");
        $stmt->execute([$id]);
        $advance_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$invoice) {
            die("Invoice not found.");
        }
    } else {
        die("No invoice ID provided.");
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #080710;
        }
        .invoice {
            background-color: white;
            padding: 0px 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .invoice-header {
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .invoice-body {
            padding: 20px;
        }
        .invoice-footer {
            padding: 20px;
            border-top: 1px solid #ddd;
        }
        .invoice-total {
            font-size: 2em;
            color: #4caf50;
        }
        .address {
            font-weight: 600;
        }
        table tr th {
            font-size: 18px;
        }
        table tr td {
            text-align: center;
        }
        table tr td input {
            border: none;
            text-align: center;
        }
        input{
            border: none;
        }
    </style>
</head>
<body>
    <form id="invoice-form" method="post" action="update_invoice.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($invoice['id']); ?>">
        <div class="container" id="invoice-table">
            <div class="invoice">
                <div class="text-center">
                    <h2>Edit Invoice</h2>
                </div>
                <hr>
                <div class="invoice-header">
                    <img src="logo.png" alt="PPFW" width="170px">
                    <div class="text-right">
                        <p class="address">H.No:-2, 3-183/1, Rd Number 1/F, <br>Beside Maisamma Temple,<br>Adarsh Nagar Colony, Nagole, <br>Hyderabad, Telangana 500068. </p>
                    </div>
                </div>
                <div class="invoice-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Billed To:</strong> <input value="<?php echo htmlspecialchars($invoice['billed_to']); ?>" type="text" name="billed_to" required><br>
                            <strong>Invoice Number:</strong> <input value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" type="text" id="invoice-number" name="invoice_number" readonly><br>
                            <strong>Date Of Issue:</strong> <input type="date" id="issue-date" name="issue_date" value="<?php echo htmlspecialchars($invoice['issue_date']); ?>" required><br>
                        </div>
                        <div class="col-sm-6 text-right">
                            <strong>Project Name:</strong> <input value="<?php echo htmlspecialchars($invoice['project_name']); ?>" type="text" name="project_name" required><br>
                            <strong>Scope of Work:</strong> <input value="<?php echo htmlspecialchars($invoice['scope_of_work']); ?>" type="text" name="scope_of_work" required><br>
                            <strong>Description:</strong> <input value="<?php echo htmlspecialchars($invoice['description']); ?>" type="text" name="description" required>
                        </div>
                    </div>
                    
                    <table class="table mt-4" id="invoice-table-content">
                        <thead>
                            <tr>
                                <th class="text-center">Description</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Payment Mode</th>
                                <th class="text-center">Reference ID</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-rows">
                            <tr>
                                <td class="text-center"><strong>Total Project Value</strong></td>
                                <td class="text-center"></td>
                                <td class="text-center"> <input value="<?php echo htmlspecialchars($invoice['total_project_value']); ?>" type="text" name="total_project_value" required></td>
                            </tr>
                            <?php foreach ($advance_payments as $index => $payment) : ?>
                            <tr>
                                <td class="text-center"><strong>Advance Payment <?php echo $index + 1; ?></strong></td>
                                <td class="text-center"> <input type="date" name="advance_payment_date[]" value="<?php echo htmlspecialchars($payment['date'] ?? ''); ?>"></td>
                                <td class="text-center"> <input value="<?php echo htmlspecialchars($payment['amount'] ?? ''); ?>" type="text" name="advance_payment_amount[]"></td>
                                <td class="text-center"><input value="<?php echo htmlspecialchars($payment['payment_mode'] ?? ''); ?>" type="text" name="advance_payment_payment_mode[]"></td>
                                <td class="text-center"><input value="<?php echo htmlspecialchars($payment['reference_id'] ?? ''); ?>" type="text" name="advance_payment_reference_id[]"></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td class="text-center"><strong>Total Received</strong></td>
                                <td class="text-center"></td>
                                <td class="text-center"> <input value="<?php echo htmlspecialchars($invoice['total_received']); ?>" type="text" name="total_received" required></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                            <tr>
                                <td class="text-center"><strong>Pending Balance</strong></td>
                                <td class="text-center"></td>
                                <td class="text-center"> <input value="<?php echo htmlspecialchars($invoice['pending_balance']); ?>" type="text" name="pending_balance" required></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="container mt-4">
                    <strong>PAY TO:</strong><br>
                    <p>PPFW WEB DEVELOPERS<br>
                    HDFC BANK<br>
                    <strong>Account Name:</strong> PPFW WEB DEVELOPERS<br>
                    <strong>Account No:</strong> 99969111786786</p>
                </div>

                <div class="invoice-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <small>This is a computer-generated invoice and needs no signature.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <center><button type="button" class="btn btn-secondary" id="add-row">Add Row</button>&nbsp;<button type="submit" class="btn btn-primary">Update Invoice</button></center>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('add-row');
    const tableBody = document.getElementById('invoice-rows');
    let rowCounter = tableBody.querySelectorAll('tr').length - 3; // Subtract 3 to exclude header and footer rows

    addButton.addEventListener('click', function() {
        rowCounter++;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="text-center"><strong>Advance Payment ${rowCounter}</strong></td>
            <td class="text-center"><input type="date" name="advance_payment_date[]" value="${new Date().toISOString().split('T')[0]}"></td>
            <td class="text-center"><input type="text" name="advance_payment_amount[]" class="amount-input"></td>
            <td class="text-center"><input value="" type="text" name="advance_payment_payment_mode[]"></td>
            <td class="text-center"><input value="" type="text" name="advance_payment_reference_id[]"></td>
        `;

        // Find the "Advance Received" row
        const advanceReceivedRow = tableBody.querySelectorAll('tr').length > 2 
            ? tableBody.querySelectorAll('tr')[tableBody.querySelectorAll('tr').length - 2] 
            : null;

        if (advanceReceivedRow) {
            tableBody.insertBefore(newRow, advanceReceivedRow);
        } else {
            tableBody.appendChild(newRow); // Fallback if no rows are found
        }

        updateTotals();
    });

    tableBody.addEventListener('input', function(event) {
        if (event.target.classList.contains('amount-input')) {
            updateTotals();
        }
    });

    function updateTotals() {
        let totalProjectValue = parseFloat(document.querySelector('input[name="total_project_value"]').value) || 0;
        let totalReceived = 0;

        document.querySelectorAll('input[name="advance_payment_amount[]"]').forEach(input => {
            totalReceived += parseFloat(input.value) || 0;
        });

        document.querySelector('input[name="total_received"]').value = totalReceived.toFixed(2);
        document.querySelector('input[name="pending_balance"]').value = (totalProjectValue - totalReceived).toFixed(2);
    }
});

    </script>

</body>
</html>
