<?php
// View/check_session.php
session_start();

header('Content-Type: application/json');

$sessionData = [
    'test_otp' => $_SESSION['test_otp'] ?? 'Not set',
    'test_phone' => $_SESSION['test_phone'] ?? 'Not set',
    'user_id' => $_SESSION['user_id'] ?? 'Not logged in',
    'username' => $_SESSION['username'] ?? 'Not set',
    'logged_in' => $_SESSION['logged_in'] ?? false
];

echo json_encode($sessionData, JSON_PRETTY_PRINT);
?>