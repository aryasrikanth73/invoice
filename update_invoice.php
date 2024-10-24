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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $billed_to = $_POST['billed_to'];
            $invoice_number = $_POST['invoice_number'];
            $issue_date = $_POST['issue_date'];
            $project_name = $_POST['project_name'];
            $scope_of_work = $_POST['scope_of_work'];
            $description = $_POST['description'];
            $total_project_value = $_POST['total_project_value'];
            $total_received = $_POST['total_received'];
            $pending_balance = $_POST['pending_balance'];

            // Update the invoice details
            $stmt = $pdo->prepare("UPDATE invoices SET billed_to = ?, invoice_number = ?, issue_date = ?, project_name = ?, scope_of_work = ?, description = ?, total_project_value = ?, total_received = ?, pending_balance = ? WHERE id = ?");
            $stmt->execute([$billed_to, $invoice_number, $issue_date, $project_name, $scope_of_work, $description, $total_project_value, $total_received, $pending_balance, $id]);

            // Handle advance payments if applicable
            // Handle advance payments if applicable
if (isset($_POST['advance_payment_date']) && isset($_POST['advance_payment_amount'])) {
    $advance_payment_dates = $_POST['advance_payment_date'];
    $advance_payment_amounts = $_POST['advance_payment_amount'];
    $payment_modes = $_POST['advance_payment_payment_mode'];
    $reference_ids = $_POST['advance_payment_reference_id'];

    // Remove old advance payments
    $stmt = $pdo->prepare("DELETE FROM advance_payments WHERE invoice_id = ?");
    $stmt->execute([$id]);

    // Insert new advance payments
    $stmt = $pdo->prepare("INSERT INTO advance_payments (invoice_id, date, amount, payment_mode, reference_id) VALUES (?, ?, ?, ?, ?)");
    for ($i = 0; $i < count($advance_payment_dates); $i++) {
        $stmt->execute([$id, $advance_payment_dates[$i], $advance_payment_amounts[$i], $payment_modes[$i], $reference_ids[$i]]);
    }
}


            // Redirect to the invoice list page after updating
            header("Location: invoice-list.php");
            exit();
        } else {
            die("No invoice ID provided.");
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
