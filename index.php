<?php
session_start();
if(!isset($_SESSION['id'],$_SESSION['email'])){
    header('location:login.php');
}
// Database configuration
$host = 'localhost';
$db = 'invoice_system';
$user = 'root';
$pass = '';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query for total amounts
    $totalQuery = $pdo->query("SELECT SUM(total_received) as total_received, SUM(pending_balance) as pending_balance FROM invoices");
    $totalResult = $totalQuery->fetch(PDO::FETCH_ASSOC);

    // Query for current month's amounts
    $monthQuery = $pdo->query("SELECT SUM(total_received) as total_received, SUM(pending_balance) as pending_balance 
                               FROM invoices 
                               WHERE MONTH(issue_date) = MONTH(CURRENT_DATE()) AND YEAR(issue_date) = YEAR(CURRENT_DATE())");
    $monthResult = $monthQuery->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #080710;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .side-menu {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0; /* Make the side menu visible by default */
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
            margin-left: 50px;
            cursor: pointer;
        }

        .open-btn {
            font-size: 30px;
            cursor: pointer;
            background-color: #343a40;
            color: white;
            padding: 10px 15px;
            border: none;
            display: none; /* Hide the open button since the menu is visible by default */
        }

        #main-content {
            transition: margin-left .3s;
            padding: 16px;
        }

        .chart-container {
            margin-top: 20px;
        }

        .chart-container h3 {
            margin-bottom: 15px;
            font-size: medium;
            text-align: center;
        }

        .chart-container canvas {
            max-width: 100%;
            height: 250px; /* Adjust the height as needed */
        }

        @media (max-width: 767.98px) {
            .side-menu {
                width: 100%;
                left: -100%;
            }

            .open-btn {
                display: block; /* Show the open button on smaller screens */
                font-size: 24px;
                padding: 10px 15px;
            }
        }
        .side-menu img {
            width: 100%;
            height: auto;
            padding: 10px;
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
    <div id="side-menu" class="side-menu">
        <span class="close-btn" onclick="closeMenu()">&times;</span>
        <img src="logo.png" alt="Logo">
        <a href="generate-invoice.php">Create Invoice</a>
        <a href="invoice-list.php">Invoice List</a>
        <a href="invoice-report.php">Invoice Report</a>
    </div>

    <button class="open-btn" onclick="toggleMenu()">&#9776; Menu</button>

    <div id="main-content">
        <div class="container">
            <div class="row chart-container">
                <div class="col-md-6">
                    <h3>Overall Financial Summary</h3>
                    <canvas id="totalChart"></canvas>
                </div>
                <div class="col-md-6">
                    <h3>This Month's Financial Summary</h3>
                    <canvas id="monthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const sideMenu = document.getElementById("side-menu");
            const mainContent = document.getElementById("main-content");
            const openBtn = document.querySelector(".open-btn");

            if (sideMenu.style.left === "0px") {
                // Close the menu
                sideMenu.style.left = "-250px";
                mainContent.style.marginLeft = "0";
                openBtn.style.display = "block"; // Show the open button
            } else {
                // Open the menu
                sideMenu.style.left = "0";
                mainContent.style.marginLeft = "250px";
                openBtn.style.display = "none"; // Hide the open button
            }
        }

        function closeMenu() {
            const sideMenu = document.getElementById("side-menu");
            const mainContent = document.getElementById("main-content");
            const openBtn = document.querySelector(".open-btn");

            sideMenu.style.left = "-250px";
            mainContent.style.marginLeft = "0";
            openBtn.style.display = "block"; // Show the open button
        }

        window.onload = function() {
            const totalData = {
                labels: ['Total Received', 'Pending Balance'],
                datasets: [{
                    label: 'Overall',
                    data: [<?php echo $totalResult['total_received']; ?>, <?php echo $totalResult['pending_balance']; ?>],
                    backgroundColor: ['#4caf50', '#f44336'],
                }]
            };

            const monthData = {
                labels: ['Total Received', 'Pending Balance'],
                datasets: [{
                    label: 'This Month',
                    data: [<?php echo $monthResult['total_received']; ?>, <?php echo $monthResult['pending_balance']; ?>],
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
