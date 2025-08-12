<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['message'] = "Please login to make a reservation.";
    header("Location: ../View/login.php");
    exit();
}

require_once '../Model/pdov2.php';
require_once '../Model/makereservationmodal.php';

// Get user details from session
$reservation_data = [
    'numberofguest' => $_POST['numberofguest'] ?? 0,
    'name' => $_POST['name'] ?? $_SESSION['username'],
    'email' => $_POST['email'] ?? $_SESSION['email'],
    'phone' => $_POST['phone'] ?? '',
    'address' => $_POST['address'] ?? '',
    'date' => $_POST['date'] ?? '',
    'session' => $_POST['session'] ?? '',
    'branch' => $_POST['branch'] ?? ''
];

// Validate future date
if (strtotime($reservation_data['date']) < strtotime('today')) {
    $_SESSION['message'] = "Please select a future date for reservation.";
    header("Location: ../View/indexv2.php");
    exit();
}

// Make reservation
$result = make_reservation_in_db($pdo, $reservation_data);

if ($result['success']) {
    $_SESSION['message'] = strtoupper($reservation_data['name']) . ", thank you! Your reservation for " . 
                          $reservation_data['numberofguest'] . " guests is confirmed. " .
                          "Total: ৳" . number_format($result['total_price'], 2) . 
                          " (Reservation ID: #" . $result['reservation_id'] . ")";
} else {
    $_SESSION['message'] = "Sorry, there was an error making your reservation. Please try again.";
}

header("Location: ../View/indexv2.php");
exit();