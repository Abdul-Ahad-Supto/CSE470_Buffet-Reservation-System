<?php
// /Controller/makereservationcontroller.php

// These lines are for debugging. They will show you any new errors.
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Go up one level (from Controller) and down into Model to include files
require_once '../Model/pdov2.php';
require_once '../Model/makereservationmodal.php';

// Get all the data from the form into an array
$reservation_data = [
    'numberofguest' => $_POST['numberofguest'] ?? 0,
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'address' => $_POST['address'] ?? '',
    'date' => $_POST['date'] ?? '',
    'session' => $_POST['session'] ?? '',
    'branch' => $_POST['branch'] ?? ''
];

// Call the function from the Model, passing the database connection and the data
$success = make_reservation_in_db($pdo, $reservation_data);

// Set the session message based on success or failure
if ($success) {
    $_SESSION['message'] = strtoupper($reservation_data['name']) . ", thank you! Your reservation for " . htmlspecialchars($reservation_data['numberofguest']) . " guests is confirmed.";
} else {
    $_SESSION['message'] = "Sorry, there was an error making your reservation. Please check your details and try again.";
}

// Redirect back to the view page
header("Location: /CSE470_Buffet-Reservation-System-main/Online Buffet Reservation System/MVC Structure Codes/View/indexv2.php");
exit(); // Always call exit() after a header redirect.