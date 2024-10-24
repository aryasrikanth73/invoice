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

    // Fetch invoices from the database
    $stmt = $pdo->query("SELECT * FROM invoices");
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Invoice List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #080710;
            color:#FFFFFF;
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
        .side-menu {
      height: 100%;
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #343a40;
      padding-top: 60px;
      transition: 0.3s;
      z-index: 1000;
    }

    .side-menu a {
      padding: 10px 15px;
      text-decoration: none;
      font-size: 18px;
      color: #fff;
      display: block;
      transition: 0.3s;
    }

    .side-menu a:hover {
      background-color: #575757;
    }

    .side-menu .close-btn {
      position: absolute;
      top: 10px;
      right: 25px;
      font-size: 36px;
      cursor: pointer;
    }

    .open-btn {
      font-size: 30px;
      cursor: pointer;
      background-color: #343a40;
      color: white;
      padding: 10px 15px;
      border: none;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1001;
    }

    #main-content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s;
    }
    </style>
</head>
<body>
<div id="side-menu" class="side-menu">
    <span class="close-btn" onclick="closeMenu()">&times;</span>
    <img src="logo.png" alt="Logo" style="width: 100px; margin-left: 15px;">
    <a href="generate-invoice.php">Create Invoice</a>
    <a href="invoice-list.php">Invoice List</a>
    <a href="invoice-report.php">Invoice Report</a>
  </div>

  <button class="open-btn" onclick="toggleMenu()">&#9776; Menu</button>

  <div id="main-content">
    <div class="container-fluid">
        <h1 class="text-center">Invoice List</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Billed To</th>
                        <th>Invoice Number</th>
                        <th>Issue Date</th>
                        <th>Project Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['billed_to']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['issue_date']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['project_name']); ?></td>
                        <td>
                            <center><a href="edit_invoice.php?id=<?php echo htmlspecialchars($invoice['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="generate_pdf.php?id=<?php echo htmlspecialchars($invoice['id']); ?>" class="btn btn-primary btn-sm" target="_blank">PDF</a>
                            </center>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

  <script>
    function toggleMenu() {
      const sideMenu = document.getElementById("side-menu");
      const mainContent = document.getElementById("main-content");
      const openBtn = document.querySelector(".open-btn");

      if (sideMenu.style.left === "0px" || sideMenu.style.left === "") {
        // Close the menu
        sideMenu.style.left = "-250px";
        mainContent.style.marginLeft = "0";
        openBtn.style.display = "block";
      } else {
        // Open the menu
        sideMenu.style.left = "0";
        mainContent.style.marginLeft = "250px";
        openBtn.style.display = "none";
      }
    }

    function closeMenu() {
      const sideMenu = document.getElementById("side-menu");
      const mainContent = document.getElementById("main-content");
      const openBtn = document.querySelector(".open-btn");

      sideMenu.style.left = "-250px";
      mainContent.style.marginLeft = "0";
      openBtn.style.display = "block";
    }

    window.onload = function() {
      const totalData = {
        labels: ['Total Received', 'Pending Balance'],
        datasets: [{
          label: 'Overall',
          data: [/* <?php echo $totalResult['total_received']; ?> */, /* <?php echo $totalResult['pending_balance']; ?> */],
          backgroundColor: ['#4caf50', '#f44336'],
        }]
      };

      const monthData = {
        labels: ['Total Received', 'Pending Balance'],
        datasets: [{
          label: 'This Month',
          data: [/* <?php echo $monthResult['total_received']; ?> */, /* <?php echo $monthResult['pending_balance']; ?> */],
          backgroundColor: ['#2196f3', '#ff9800'],
        }]
      };

      const configTotal = {
        type: 'doughnut',
        data: totalData,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
          }
        },
      };

      const configMonth = {
        type: 'doughnut',
        data: monthData,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
          }
        },
      };

      const totalCtx = document.getElementById('totalChart').getContext('2d');
      new Chart(totalCtx, configTotal);

      const monthCtx = document.getElementById('monthChart').getContext('2d');
      new Chart(monthCtx, configMonth);
    };
  </script>
</body>
</html>
