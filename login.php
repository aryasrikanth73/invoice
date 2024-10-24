<?php
include('connection.php');

$error = ''; // Initialize error variable

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Password input

    // Query to select the admin by email
    $sql = "SELECT * FROM `admin` WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = mysqli_fetch_assoc($result);
            

            // Assuming password is stored in plain text for this example
            if ($row['password'] === $password) { // Check if the input password matches the stored password
                session_start();
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                header('location:index.php');
                exit();
            } else {
                $error = "Invalid Credentials"; // Password does not match
            }
        } else {
            $error = "Invalid Credentials"; // Email not found
        }
    } else {
        die(mysqli_error($conn)); // Database query error
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<style>
    /* Your existing CSS here */
    .error {
        color: red;
        margin-top: 5%;
        text-align: center;
    }

    /* Existing CSS here */
    body {
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #080710;
    }

    .background {
        position: absolute;
        width: 430px;
        height: 520px;
        background: linear-gradient(135deg, #C850C0, #4158D0);
        border-radius: 10px;
        transform: rotate(45deg);
        top: -80px;
        left: -80px;
    }

    .background .shape {
        height: 200px;
        width: 200px;
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, #C850C0, #4158D0);
    }

    .shape:first-child {
        top: -100px;
        left: -100px;
    }

    .shape:last-child {
        bottom: -80px;
        right: -90px;
    }

    form {
        height: 520px;
        width: 400px;
        background-color: rgba(255, 255, 255, 0.13);
        border-radius: 10px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
        padding: 50px 35px;
        display: none;
        /* Hide both forms initially */
        flex-direction: column;
        justify-content: center;
        transition: all 0.5s ease;
        /* Add transition for animation */
    }

    form.active {
        display: flex;
        /* Show active form */
    }

    form h3 {
        font-size: 32px;
        font-weight: 500;
        line-height: 42px;
        color: #ffffff;
        text-align: center;
    }

    form label {
        margin-top: 30px;
        font-size: 16px;
        color: #ffffff;
    }

    form input {
        height: 50px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.07);
        border: none;
        border-radius: 3px;
        padding: 0 10px;
        margin-top: 8px;
        font-size: 14px;
        font-weight: 300;
        color: #e5e5e5;
    }

    form button {
        margin-top: 50px;
        width: 100%;
        background-color: #ffffff;
        color: #080710;
        padding: 15px 0;
        font-size: 18px;
        font-weight: 600;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .social {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }

    .social div {
        background-color: rgba(255, 255, 255, 0.27);
        border-radius: 3px;
        padding: 10px 10px;
        font-size: 14px;
        text-align: center;
        color: #eaf0fb;
        cursor: pointer;
        width: 48%;
        transition: background-color 0.3s ease;
    }

    .social div.active {
        background-color: #ffffff;
        color: #080710;
    }

    .social div i {
        margin-right: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        form {
            width: 300px;
            padding: 40px 30px;
        }

        .background {
            width: 320px;
            height: 400px;
            top: -50px;
            left: -60px;
        }
    }

    @media (max-width: 480px) {
        form {
            width: 350px;
            padding: 30px 20px;
        }

        .background {
            width: 280px;
            height: 350px;
            top: -40px;
            left: -50px;
        }

        form h3 {
            font-size: 28px;
        }

        form label {
            margin-top: 20px;
            font-size: 14px;
        }

        form input {
            height: 45px;
            font-size: 12px;
        }

        form button {
            margin-top: 40px;
            padding: 12px 0;
            font-size: 16px;
        }

        .social div {
            font-size: 12px;
            padding: 8px 5px;
        }
    }

    .error {
        color: red;
        margin-top: 5%;
        text-align: center;
        font-weight: 800;
    }
</style>
<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Admin Login Form -->
    <form id="admin" method="post" class="active">
        <h3>Admin Login</h3>

        <label for="admin-username">Email</label>
        <input type="text" placeholder="Email" id="admin-username" name="email" required>

        <label for="admin-password">Password</label>
        <input type="password" placeholder="Password" id="admin-password" name="password" required>

        <button type="submit" name="submit">Log In</button>

        <div class="social">
            <div class="go active" onclick="showAdmin()"><i class="fas fa-user-shield"></i> Admin</div>&nbsp;
            <div class="fb" onclick="showEmploy()"><i class="fas fa-user"></i> Employ</div>
        </div>

        <!-- Display Error Message -->
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </form>

    <!-- Employ Login Form -->
    <form id="employ" method="post">
        <h3>Employ Login</h3>

        <label for="employ-username">Email</label>
        <input type="text" placeholder="Email" id="employ-username" name="email1" required>

        <label for="employ-password">Password</label>
        <input type="password" placeholder="Password" id="employ-password" name="password1" required>

        <button type="submit">Log In</button>
        <div class="social">
            <div class="go" onclick="showAdmin()"><i class="fas fa-user-shield"></i> Admin</div>&nbsp;
            <div class="fb active" onclick="showEmploy()"><i class="fas fa-user"></i> Employ</div>
        </div>
    </form>

    <script>
        // JavaScript to toggle forms and highlight buttons
        function showAdmin() {
            document.getElementById("admin").classList.add("active");
            document.getElementById("employ").classList.remove("active");
        }

        function showEmploy() {
            document.getElementById("employ").classList.add("active");
            document.getElementById("admin").classList.remove("active");
        }
    </script>
</body>
</html>
