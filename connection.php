<?php
$conn = new mysqli('localhost', 'root', '', 'invoice_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
// else {
//     echo "Connection successful!";
// }
?>
