<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'admin'); // Change this to your actual database password... default is: "" -> no password
define('DB_NAME', 'bikehub_db');
define("Port", "3307");
//Uncomment the line below and comment the line above if you use port 3306 instead of 3307
//define("Port", "3306");


// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, Port);

// Check connection
if (!$conn) {
    die('<p style="font-family:sans-serif;color:red;padding:20px">
        <strong>Database connection failed:</strong> '
        . mysqli_connect_error() . '</p>');
}
// Set charset to UTF-8
$conn->set_charset("utf8");
?>