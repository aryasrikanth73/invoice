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

    // Fetch the current invoice number from the database
    $stmt = $pdo->query("SELECT current_number FROM invoice_counter WHERE id = 1");
    $currentNumber = $stmt->fetchColumn();

    // Format the invoice number
    $incrementingNumber = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
    $formattedInvoiceDate = date('dmy');
    $invoiceNumber = "ppfw{$incrementingNumber}-{$formattedInvoiceDate}";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
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
             /* CSS for the logout button */
.logout-button {
    position: fixed;
    top: 10px; /* Adjust as needed */
    right: 10px; /* Adjust as needed */
    background-color: #f44336; /* Red background color */
    color: white; /* White text color */
    padding: 10px 20px; /* Padding for the button */
    font-size: 16px; /* Font size */
    font-weight: bold; /* Font weight */
    text-decoration: none; /* Remove underline */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions */
}

.logout-button:hover {
    background-color: #e53935; /* Darker red on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}

.logout-button:active {
    background-color: #d32f2f; /* Even darker red on click */
    transform: scale(0.95); /* Slightly decrease size on click */
}
        </style>
</head>
<body>
<a href="logout.php" class="logout-button">Logout</a>
    <form id="invoice-form" method="post" action="save_pdf.php">
        <div class="container" id="invoice-table">
            <div class="invoice">
                <div class="text-center">
                    <h2>Invoice</h2>
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
                        <div class="col-sm-5">
                            <strong>Billed To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <input value="Srikanth" type="text" name="billed_to" required><br>
                            <strong>Invoice Number&nbsp;:</strong> <input value="<?= $invoiceNumber ?>" type="text" id="invoice-number" name="invoice_number" readonly><br>
                            <strong>Date Of Issue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <input type="date" id="issue-date" name="issue_date" required><br>
                        </div>
                        <div class="col-sm-3"></div>
                        <div class="col-sm-4">
                            <strong>Project Name&nbsp;&nbsp;:</strong> <input value="Arya" type="text" name="project_name" required><br>
                            <strong>Scope of Work:</strong> <input value="qwerty" type="text" name="scope_of_work" required><br>
                            <strong>Description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <input value="Wikipedia is a free online encyclopedia, created and edited by volunteers around the world and hosted by the Wikimedia Foundation." type="text" name="description" required>
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
                        <tbody>
                            <tr>
                                <td><strong>Total Project Value</strong></td>
                                <td></td>
                                <td><input id="total_project_value" value="200000" type="text" name="total_project_value" required></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr id="advance-payment-row">
                                <td><strong>Advance Payment 1</strong></td>
                                <td><input id="advance_payment_date" type="date" name="advance_payment_date"></td>
                                <td><input id="advance_payment_amount" value="50000" type="text" name="advance_payment_amount"></td>
                                <td><input value="cash" type="text" name="payment_mode" required></td>
                                <td><input value="1235874" type="text" name="reference_id"></td>
                            </tr>
                            <tr>
                                <td><strong>Total Received</strong></td>
                                <td></td>
                                <td><input id="total_received" value="50000" type="text" name="total_received" readonly></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>Pending Balance</strong></td>
                                <td></td>
                                <td><input id="pending_balance" value="150000" type="text" name="pending_balance" readonly></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
        <center><button type="submit">Submit</button></center>
    </form>
    <!-- Include html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        window.onload = function() {
            const date = new Date();
            const formattedDate = date.toISOString().slice(0, 10); // Format to YYYY-MM-DD

            // Set current date to "Date Of Issue" and "Advance Payment 1"
            document.getElementById('issue-date').value = formattedDate;
            document.getElementById('advance_payment_date').value = formattedDate;

            // Add event listeners to the input fields for automatic calculation
            document.getElementById('total_project_value').addEventListener('input', calculateTotals);
            document.getElementById('advance_payment_amount').addEventListener('input', calculateTotals);
        };

        // Function to calculate totals and update the fields
        function calculateTotals() {
            const totalProjectValue = parseFloat(document.getElementById('total_project_value').value) || 0;
            const advancePaymentAmount = parseFloat(document.getElementById('advance_payment_amount').value) || 0;

            // Set total_received equal to advance_payment_amount
            document.getElementById('total_received').value = advancePaymentAmount.toFixed(2);

            // Calculate pending_balance
            const pendingBalance = totalProjectValue - advancePaymentAmount;
            document.getElementById('pending_balance').value = pendingBalance.toFixed(2);
        }
    </script>
</body>
</html>