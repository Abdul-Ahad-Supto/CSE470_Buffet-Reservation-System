<?php
// /Model/feedbackmodal.php

function save_feedback_in_db($pdo, $data) {
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO feedback (firstname, lastname, tel_number, email, may_contact, contact_method, feedback_message)
             VALUES (:fname, :lname, :tel, :email, :maycontact, :contactmethod, :message)'
        );
        $stmt->execute([
            ':fname' => $data['firstname'],
            ':lname' => $data['lastname'],
            ':tel' => $data['telnum'],
            ':email' => $data['email'],
            ':maycontact' => $data['may_contact'],
            ':contactmethod' => $data['contact_method'],
            ':message' => $data['feedback']
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}