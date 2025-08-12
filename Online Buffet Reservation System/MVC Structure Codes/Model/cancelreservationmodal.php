<?php
// /Model/cancelreservationmodal.php

function cancel_reservation_in_db($pdo, $data) {
    $branch = strtoupper($data['branch']);
    $session = strtoupper($data['session']);
    $name = strtoupper($data['name']);

    try {
        // NOTE: Added customer_phone to the WHERE clause for better security, 
        // to prevent cancelling someone else's reservation with a similar name.
        $stmt = $pdo->prepare(
            'DELETE FROM Reservation_details 
             WHERE Customer_name = :uid AND Reservation_date = :fn AND branch_name = :ln AND Session = :em'
        );
        $stmt->execute([
            ':uid' => $name,
            ':fn' => $data['date'],
            ':ln' => $branch,
            ':em' => $session
        ]);
        // Check if a row was actually deleted and return true or false
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}