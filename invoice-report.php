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

    // Fetch months for dropdown
    $monthQuery = $pdo->query("SELECT DISTINCT DATE_FORMAT(issue_date, '%Y-%m') AS month FROM invoices ORDER BY month DESC");
    $months = $monthQuery->fetchAll(PDO::FETCH_ASSOC);

    // Handle month selection
    $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('Y-m');

    // Fetch data for the selected month
    $stmt = $pdo->prepare("SELECT project_name, scope_of_work, total_project_value, total_received, created_at 
                            FROM invoices 
                            WHERE DATE_FORMAT(issue_date, '%Y-%m') = :selectedMonth");
    $stmt->execute(['selectedMonth' => $selectedMonth]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $totalProjectValue = 0;
    $totalReceived = 0;

    foreach ($invoices as $invoice) {
        $totalProjectValue += $invoice['total_project_value'];
        $totalReceived += $invoice['total_received'];
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
    <title>Invoice Report</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #080710;
            color: #FFFFFF;
        }
        .container {
            margin-top: 20px;
        }
        table {
            margin-top: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        tfoot td {
            font-weight: bold;
        }
        .form-group label {
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Invoice Report</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="month">Select Month:</label>
                <select name="month" id="month" class="form-control">
                    <?php foreach ($months as $month): ?>
                        <option value="<?php echo $month['month']; ?>" <?php echo $selectedMonth === $month['month'] ? 'selected' : ''; ?>>
                            <?php echo date('F Y', strtotime($month['month'] . '-01')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Scope of Work</th>
                        <th>Total Project Value</th>
                        <th>Total Received</th>
                        <th>Created At</th>
                        <th>Full details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($invoices) > 0): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invoice['project_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['scope_of_work']); ?></td>
                                <td><?php echo number_format($invoice['total_project_value'], 2); ?></td>
                                <td><?php echo number_format($invoice['total_received'], 2); ?></td>
                                <td><?php echo htmlspecialchars($invoice['created_at']); ?></td>
                                <td>view button</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No records found for the selected month.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td><?php echo number_format($totalProjectValue, 2); ?></td>
                        <td><?php echo number_format($totalReceived, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
