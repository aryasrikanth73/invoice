<?php
$success = "";
session_start();
if(!isset($_SESSION['id'],$_SESSION['email'])){
    header('location:login.php');
}
// Database configuration
$host = 'localhost'; // or your database host
$db = 'invoice_system';
$user = 'root'; // or your database username
$pass = ''; // or your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start a transaction
    $pdo->beginTransaction();

    // Retrieve form data
    $billed_to = $_POST['billed_to'];
    $invoice_number = $_POST['invoice_number'];
    $issue_date = $_POST['issue_date'];
    $project_name = $_POST['project_name'];
    $scope_of_work = $_POST['scope_of_work'];
    $description = $_POST['description'];
    $total_project_value = $_POST['total_project_value'];
    $advance_payment_date = $_POST['advance_payment_date'];
    $advance_payment_amount = $_POST['advance_payment_amount'];
    $payment_mode = $_POST['payment_mode'];
    $reference_id = $_POST['reference_id'];
    $total_received = $_POST['total_received'];
    $pending_balance = $_POST['pending_balance'];

    // Insert invoice data into the invoices table
    $stmt = $pdo->prepare("
        INSERT INTO invoices (billed_to, invoice_number, issue_date, project_name, scope_of_work, description, total_project_value, total_received, pending_balance)
        VALUES (:billed_to, :invoice_number, :issue_date, :project_name, :scope_of_work, :description, :total_project_value, :total_received, :pending_balance)
    ");
    $stmt->execute([
        ':billed_to' => $billed_to,
        ':invoice_number' => $invoice_number,
        ':issue_date' => $issue_date,
        ':project_name' => $project_name,
        ':scope_of_work' => $scope_of_work,
        ':description' => $description,
        ':total_project_value' => $total_project_value,
        ':total_received' => $total_received,
        ':pending_balance' => $pending_balance
    ]);

    // Get the last inserted invoice ID
    $invoice_id = $pdo->lastInsertId();

    // Insert advance payment data into the advance_payments table
    $stmt = $pdo->prepare("
        INSERT INTO advance_payments (invoice_id, date, amount, payment_mode, reference_id)
        VALUES (:invoice_id, :date, :amount, :payment_mode, :reference_id)
    ");
    $stmt->execute([
        ':invoice_id' => $invoice_id,
        ':date' => $advance_payment_date,
        ':amount' => $advance_payment_amount,
        ':payment_mode' => $payment_mode,
        ':reference_id' => $reference_id
    ]);

    // Increment the invoice number in the database
    $stmt = $pdo->prepare("UPDATE invoice_counter SET current_number = current_number + 1 WHERE id = 1");
    $stmt->execute();

    // Commit the transaction
    $pdo->commit();
    
    $success = "Invoice saved successfully!";
} catch (PDOException $e) {
    // Rollback the transaction if something goes wrong
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
         body, html {
            height: 100%; /* Ensure the body and html take up the full height of the viewport */
            margin: 0; /* Remove default margin */
            font-family: Arial, sans-serif;
            background-color: #080710;
            color: #fff;
            display: flex; /* Use flexbox to center content */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }
        h1 {
            margin: 0; /* Remove default margin from h1 */
        }
    </style>
</head>
<body>
    <h1><?php echo  $billed_to  ?>'s <?php echo  $success ?></h1>
</body>
</html>
