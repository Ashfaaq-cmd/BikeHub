<?php

session_start();
require_once 'db.php';

// Detect AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$fname   = trim($_POST['firstname'] ?? '');
$lname   = trim($_POST['lastname']  ?? '');
$email   = trim($_POST['email']     ?? '');
$phone   = trim($_POST['phone']     ?? '');
$subject = trim($_POST['subject']   ?? '');
$message = trim($_POST['message']   ?? '');
$name    = trim($fname . ' ' . $lname);

// Validation
if (empty($fname) || empty($email) || empty($subject) || empty($message)) {
    $error = 'Please fill in all required fields.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
}

// If validation failed
if (!empty($error)) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit;
    } else {
        $_SESSION['contact_error'] = $error;
        header('Location: ../contact.php');
        exit;
    }
}

// Insert into DB
$stmt = mysqli_prepare($conn,
    'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);

if (mysqli_stmt_execute($stmt)) {
    $success = "Your message has been sent! We'll be in touch soon.";
} else {
    $error = "Could not send your message. Please try again.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Final response
if ($isAjax) {
    if (!empty($success)) {
        echo json_encode(['status' => 'success', 'message' => $success]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $error]);
    }
    exit;
} else {
    if (!empty($success)) {
        $_SESSION['contact_success'] = $success;
    } else {
        $_SESSION['contact_error'] = $error;
    }
    header('Location: ../contact.php');
    exit;
}