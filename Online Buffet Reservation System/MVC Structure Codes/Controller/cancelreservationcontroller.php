<?php
// /Controller/cancelreservationcontroller.php
session_start();

require_once '../Model/pdov2.php';
require_once '../Model/cancelreservationmodal.php';

$cancellation_data = [
    'name' => $_POST['name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'date' => $_POST['date'] ?? '',
    'session' => $_POST['session'] ?? '',
    'branch' => $_POST['branch'] ?? ''
];

$success = cancel_reservation_in_db($pdo, $cancellation_data);

if ($success) {
    $_SESSION['message'] = "Your reservation has been successfully cancelled.";
} else {
    $_SESSION['message'] = "Could not find a matching reservation to cancel. Please verify your details.";
}

header("Location: /CSE470_Buffet-Reservation-System-main/Online Buffet Reservation System/MVC Structure Codes/View/indexv2.php");
exit();