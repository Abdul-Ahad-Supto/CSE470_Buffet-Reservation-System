<?php
// Model/makereservationmodal.php (Updated)
function make_reservation_in_db($pdo, $data) {
    $status = 'confirm';
    $branch = strtoupper($data['branch']);
    $session = strtoupper($data['session']);
    $name = strtoupper($data['name']);
    $user_id = $_SESSION['user_id'] ?? null;

    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert reservation
        $stmt = $pdo->prepare(
            'INSERT INTO Reservation_details (user_id, Customer_name, customer_email, customer_phone, customer_address, Reservation_date, branch_name, Session, Numberofguest, Status, total_price) 
             VALUES (:user_id, :name, :email, :phone, :address, :date, :branch, :session, :guests, :status, :price)'
        );
        
        // Calculate price based on session and guest count
        $priceStmt = $pdo->prepare("SELECT price FROM session_pricing WHERE session_name = :session");
        $priceStmt->execute([':session' => $session]);
        $priceData = $priceStmt->fetch(PDO::FETCH_ASSOC);
        $totalPrice = ($priceData['price'] ?? 0) * $data['numberofguest'];
        
        $stmt->execute([
            ':user_id' => $user_id,
            ':name' => $name,
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':date' => $data['date'],
            ':branch' => $branch,
            ':session' => $session,
            ':guests' => $data['numberofguest'],
            ':status' => $status,
            ':price' => $totalPrice
        ]);
        
        $reservation_id = $pdo->lastInsertId();
        
        // Update seat availability
        $branchStmt = $pdo->prepare("SELECT branch_id FROM branch_details WHERE branch_name = :branch");
        $branchStmt->execute([':branch' => $branch]);
        $branchData = $branchStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($branchData) {
            // Check if availability record exists
            $availStmt = $pdo->prepare("
                SELECT * FROM seat_availability 
                WHERE branch_id = :branch_id AND date = :date AND session = :session
            ");
            $availStmt->execute([
                ':branch_id' => $branchData['branch_id'],
                ':date' => $data['date'],
                ':session' => $session
            ]);
            
            if ($availStmt->rowCount() > 0) {
                // Update existing record
                $updateStmt = $pdo->prepare("
                    UPDATE seat_availability 
                    SET reserved_seats = reserved_seats + :guests 
                    WHERE branch_id = :branch_id AND date = :date AND session = :session
                ");
                $updateStmt->execute([
                    ':guests' => $data['numberofguest'],
                    ':branch_id' => $branchData['branch_id'],
                    ':date' => $data['date'],
                    ':session' => $session
                ]);
            } else {
                // Insert new record (assuming 100 total seats per session)
                $insertStmt = $pdo->prepare("
                    INSERT INTO seat_availability (branch_id, date, session, total_seats, reserved_seats)
                    VALUES (:branch_id, :date, :session, 100, :guests)
                ");
                $insertStmt->execute([
                    ':branch_id' => $branchData['branch_id'],
                    ':date' => $data['date'],
                    ':session' => $session,
                    ':guests' => $data['numberofguest']
                ]);
            }
        }
        
        // Log user activity for AI recommendations
        if ($user_id) {
            $logStmt = $pdo->prepare("
                INSERT INTO user_activity_log (user_id, action_type, reservation_date, session, guest_count, branch_id)
                VALUES (:user_id, 'reservation', :date, :session, :guests, :branch_id)
            ");
            $logStmt->execute([
                ':user_id' => $user_id,
                ':date' => $data['date'],
                ':session' => $session,
                ':guests' => $data['numberofguest'],
                ':branch_id' => $branchData['branch_id'] ?? null
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        return ['success' => true, 'reservation_id' => $reservation_id, 'total_price' => $totalPrice];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
