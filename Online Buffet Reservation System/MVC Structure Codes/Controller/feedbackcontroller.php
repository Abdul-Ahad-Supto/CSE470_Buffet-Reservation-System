<?php
// /Controller/feedbackcontroller.php

// These lines are for debugging. They will show you any new errors.
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Verify these paths are correct
require_once '../Model/pdov2.php';
require_once '../Model/feedbackmodal.php';

// Collect all the data sent from your form
$feedback_data = [
    'firstname' => $_POST['firstname'] ?? '',
    'lastname' => $_POST['lastname'] ?? '',
    'telnum' => ($_POST['countrycode'] ?? '') . ' ' . ($_POST['telnum'] ?? ''),
    'email' => $_POST['email'] ?? '',
    'may_contact' => isset($_POST['approve']) ? 1 : 0, 
    'contact_method' => $_POST['contactmethod'] ?? 'N/A',
    'feedback' => $_POST['feedback'] ?? ''
];

// Call the function in your model to save the data
$success = save_feedback_in_db($pdo, $feedback_data);

// Set the session message to be displayed on the contact page
if ($success) {
    $_SESSION['message'] = "Thank you, " . htmlspecialchars($feedback_data['firstname']) . "! Your feedback has been received.";
} else {
    $_SESSION['message'] = "Sorry, there was an error submitting your feedback. Please try again.";
}

// Verify this redirect path is correct
header("Location: /CSE470_Buffet-Reservation-System-main/Online Buffet Reservation System/MVC Structure Codes/View/contactus.php");
exit();