<?php
session_start();
if(!isset($_SESSION['id'],$_SESSION['email'])){
    header('location:login.php');
}
require 'vendor/autoload.php'; // Ensure Dompdf is installed and autoloaded

use Dompdf\Dompdf;
use Dompdf\Options;

// Database configuration
$host = 'localhost';
$db = 'invoice_system';
$user = 'root';
$pass = '';

// Dompdf options
$options = new Options();
$options->set('isRemoteEnabled', true); // Enable remote resources

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // SQL query to join invoices with advance_payments and fetch all payments for the invoice
        $stmt = $pdo->prepare("
            SELECT i.*, ap.payment_mode, ap.reference_id, ap.date AS payment_date, ap.amount AS payment_amount
            FROM invoices i
            LEFT JOIN advance_payments ap ON i.id = ap.invoice_id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            die("Invoice not found.");
        }

        // Fetch all advance payments for the invoice
        $stmt = $pdo->prepare("SELECT * FROM advance_payments WHERE invoice_id = ?");
        $stmt->execute([$id]);
        $advance_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create PDF content
        $html = '
            <h2 style="text-align:center;">Invoice</h2><hr>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <img src="https://ppfwwebdevelopers.com/wp-content/uploads/2024/08/PPFW-140x50-1.png" width="150px" style="margin-top: 30px;">
                <p style="text-align: right; margin-top: -60px;">
                   H.No:-2, 3-183/1, Rd Number 1/F, <br>
                    Beside Maisamma Temple,<br>
                    Adarsh Nagar Colony, Nagole, <br>
                    Hyderabad, Telangana 500068.
                </p>
            </div>  <br> <br> <br> <br> <br>
            <div style="float: left; width: 45%; box-sizing: border-box;">
                <p style="margin: 0 0 5px 0;">
                    <strong>Billed To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> 
                    <span>' . htmlspecialchars($invoice['billed_to']) . '</span>
                </p>
                <p style="margin: 0 0 5px 0;">
                    <strong>Invoice Number :</strong> 
                    <span>' . htmlspecialchars($invoice['invoice_number']) . '</span>
                </p>
                <p style="margin: 0 0 5px 0;">
                    <strong>Date Of Issue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> 
                    <span>' . htmlspecialchars($invoice['issue_date']) . '</span>
                </p>
            </div>

            <div style="float: right; width: 45%; box-sizing: border-box;">
                <p style="margin: 0 0 5px 0;">
                    <strong>Project Name &nbsp;:</strong> 
                    <span>' . htmlspecialchars($invoice['project_name']) . '</span>
                </p>
                <p style="margin: 0 0 5px 0;">
                    <strong>Scope of Work :</strong> 
                    <span>' . htmlspecialchars($invoice['scope_of_work']) . '</span>
                </p>
                <p style="margin: 0 0 5px 0;">
                    <strong>Description &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> 
                    <span>' . htmlspecialchars($invoice['description']) . '</span>
                </p>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 150px;">
                <thead>
                    <tr style="background-color: #007bff; color: #fff;">
                        <th style="border: 1px solid #ddd; text-align: center;">Description</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Date</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Amount</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Payment Mode</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Reference ID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><b>Total Project Value</b></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($invoice['total_project_value']) . '</td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                    </tr>';

        // Add advance payment rows to the PDF
        $counter = 1;
        foreach ($advance_payments as $payment) {
            $html .= '
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><b>Advance Payment &nbsp;' . $counter . '</b></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($payment['date']) . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($payment['amount']) . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($payment['payment_mode']) . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($payment['reference_id']) . '</td>
                </tr>';
                $counter++;
        }

        $html .= '
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><b>Total Received</b></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($invoice['total_received']) . '</td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><b>Pending Balance</b></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($invoice['pending_balance']) . '</td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"></td>
                    </tr>
                </tbody>
            </table> <br><br><br>
                        <strong>PAY TO:</strong><br>
                        <p  style="margin: 0 0 5px 0;">PPFW WEB DEVELOPERS</p>
                        <p  style="margin: 0 0 5px 0;">HDFC BANK<br></p>
                        <p  style="margin: 0 0 5px 0;"><strong>Account Name:</strong> PPFW WEB DEVELOPERS<br></p>
                        <p  style="margin: 0 0 5px 0;"><strong>Account No:</strong> 99969111786786</p>

                        <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 20px;">
                            <small>This is a computer-generated invoice and needs no signature.</small>
                        </div>
        ';

        // Initialize Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('invoice_' . $id . '.pdf', ['Attachment' => 0]); // Display PDF in browser
    } else {
        die("No invoice ID provided.");
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
