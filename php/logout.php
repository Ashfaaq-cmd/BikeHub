<?php
//Destroy session and log out user
session_start();
//clear all session variables
$_SESSION=[];
//Destroy the session
session_destroy();
//Redirect to login page
header("Location: ../login.php");
exit();
?>