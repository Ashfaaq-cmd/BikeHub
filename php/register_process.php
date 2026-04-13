<?php
//  DB columns: fname, lname, email, password, profile_img
//  Form fields: firstname, lastname, email, password, profile_img
//

session_start();

// Already logged in — skip registration
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

//  Only run on POST with the register button 
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
    header('Location: ../register.php');
    exit;
}

require_once 'db.php';

// ── Read form values 
$fname    = trim($_POST['firstname'] ?? '');
$lname    = trim($_POST['lastname']  ?? '');
$email    = trim($_POST['email']     ?? '');
$password = $_POST['password']       ?? '';

// ── Server-side validation 
// Store errors in session so register.php can display them via JS
if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
    $_SESSION['reg_error'] = 'All fields are required.';
    $_SESSION['reg_old']   = ['firstname' => $fname, 'lastname' => $lname, 'email' => $email];
    header('Location: ../register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reg_error'] = 'Please enter a valid email address.';
    $_SESSION['reg_old']   = ['firstname' => $fname, 'lastname' => $lname, 'email' => $email];
    header('Location: ../register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['reg_error'] = 'Password must be at least 6 characters.';
    $_SESSION['reg_old']   = ['firstname' => $fname, 'lastname' => $lname, 'email' => $email];
    header('Location: ../register.php');
    exit;
}

//  Check if email already exists 
$chk = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1');
mysqli_stmt_bind_param($chk, 's', $email);
mysqli_stmt_execute($chk);
mysqli_stmt_store_result($chk);

if (mysqli_stmt_num_rows($chk) > 0) {
    mysqli_stmt_close($chk);
    $_SESSION['reg_error'] = 'An account with that email already exists.';
    $_SESSION['reg_old']   = ['firstname' => $fname, 'lastname' => $lname, 'email' => $email];
    header('Location: ../register.php');
    exit;
}
mysqli_stmt_close($chk);

//  Handle profile picture upload 
$profile_img = 'default.png';

if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file     = $_FILES['profile_img'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['reg_error'] = 'File upload error. Please try again.';
        header('Location: ../register.php');
        exit;
    }

    if ($file['size'] > $max_size) {
        $_SESSION['reg_error'] = 'Profile picture must be under 5MB.';
        header('Location: ../register.php');
        exit;
    }

    // Validate real MIME type — prevents uploading a PHP file renamed as .jpg
    $finfo         = new finfo(FILEINFO_MIME_TYPE);
    $mime          = $finfo->file($file['tmp_name']);
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($mime, $allowed_mimes)) {
        $_SESSION['reg_error'] = 'Only JPG, PNG, WEBP or GIF images are allowed.';
        header('Location: ../register.php');
        exit;
    }

    $ext_map     = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $ext         = $ext_map[$mime];
    $profile_img = 'user_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    // uploads/ folder is one level up from php/
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $profile_img)) {
        $_SESSION['reg_error'] = 'Could not save profile picture. Check uploads/ folder permissions.';
        header('Location: ../register.php');
        exit;
    }
}

//  Hash password and INSERT user 
$hashed = password_hash($password, PASSWORD_BCRYPT);

// DB columns are fname, lname — NOT firstname/lastname
$stmt = mysqli_prepare($conn,
    'INSERT INTO users (fname, lname, email, password, profile_img) VALUES (?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'sssss', $fname, $lname, $email, $hashed, $profile_img);

if (!mysqli_stmt_execute($stmt)) {
    // Show the actual MySQL error to help debug
    $_SESSION['reg_error'] = 'Registration failed: ' . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    header('Location: ../register.php');
    exit;
}

$new_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);
mysqli_close($conn);

//  Log the user in immediately 
$_SESSION['user_id']    = $new_id;
$_SESSION['user_name']  = $fname . ' ' . $lname;
$_SESSION['user_email'] = $email;
$_SESSION['user_img']   = $profile_img;

// Clear any leftover error data
unset($_SESSION['reg_error'], $_SESSION['reg_old']);

header('Location: ../index.php');
exit;