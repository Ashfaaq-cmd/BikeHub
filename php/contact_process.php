<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: ../contact.html');
    exit;
}

require_once 'db.php';

$fname   = trim($_POST['firstname'] ?? '');
$lname   = trim($_POST['lastname']  ?? '');
$email   = trim($_POST['email']     ?? '');
$phone   = trim($_POST['phone']     ?? '');
$subject = trim($_POST['subject']   ?? '');
$message = trim($_POST['message']   ?? '');
$name    = trim($fname . ' ' . $lname);

if (empty($fname) || empty($email) || empty($subject) || empty($message)) {
    $_SESSION['contact_error'] = 'Please fill in all required fields.';
    header('Location: ../contact.html');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Please enter a valid email address.';
    header('Location: ../contact.html');
    exit;
}

$stmt = mysqli_prepare($conn,
    'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION['contact_success'] = 'Your message has been sent! We\'ll be in touch soon.';
} else {
    mysqli_stmt_close($stmt);
    $_SESSION['contact_error'] = 'Could not send your message. Please try again.';
}

header('Location: ../contact.html');
exit;