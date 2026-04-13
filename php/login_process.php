<?php
// php/login.php
// ─────────────────────────────────────────────────────────────
//  Handles login.php form.
//  DB columns: fname, lname, email, password, profile_img
// ─────────────────────────────────────────────────────────────

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
    header('Location: ../login.php');
    exit;
}

require_once 'db.php';

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';

//  Validation 
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email and password are required.';
    header('Location: ../login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Please enter a valid email address.';
    header('Location: ../login.php');
    exit;
}

//  Retrieve user — SELECT fname + lname
$stmt = mysqli_prepare($conn,
    'SELECT id, fname, lname, email, password, profile_img FROM users WHERE email = ? LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Verify password 
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'Incorrect email or password.';
    header('Location: ../login.php');
    exit;
}

//Set session 
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['fname'] . ' ' . $user['lname'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_img']   = $user['profile_img'] ?? 'default.png';

unset($_SESSION['login_error']);
mysqli_close($conn);

header('Location: ../index.php');
exit;