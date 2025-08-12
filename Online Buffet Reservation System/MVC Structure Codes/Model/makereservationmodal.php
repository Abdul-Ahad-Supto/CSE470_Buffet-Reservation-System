<?php
// /Model/makereservationmodal.php

function make_reservation_in_db($pdo, $data) {
    $status = 'confirm';
    $branch = strtoupper($data['branch']);
    $session = strtoupper($data['session']);
    $name = strtoupper($data['name']);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO Reservation_details (Customer_name, Reservation_date, branch_name, Session, Numberofguest, Status) 
             VALUES (:uid, :fn, :ln, :em, :he, :su)'
        );
        $stmt->execute([
            ':uid' => $name,
            ':fn' => $data['date'],
            ':ln' => $branch,
            ':em' => $session,
            ':he' => $data['numberofguest'],
            ':su' => $status
        ]);
        
        $stmt2 = $pdo->prepare(
            'INSERT INTO Customer_info (customer_name, customer_email, customer_phone, customer_address) 
             VALUES (:cn, :ce, :cp, :ca)'
        );
        $stmt2->execute([
            ':cn' => $name,
            ':ce' => $data['email'],
            ':cp' => $data['phone'],
            ':ca' => $data['address']
        ]);
        return true; // Return true on success
    } catch (PDOException $e) {
        // For debugging, you could log the error: error_log($e->getMessage());
        return false; // Return false on failure
    }
}