<?php
// Model/seatstatusmodal.php
class SeatStatusModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get seat availability for a specific date
    public function getSeatStatus($date = null, $branch_id = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        try {
            $query = "
                SELECT sa.*, bd.branch_name 
                FROM seat_availability sa
                JOIN branch_details bd ON sa.branch_id = bd.branch_id
                WHERE sa.date = :date
            ";
            
            $params = [':date' => $date];
            
            if ($branch_id) {
                $query .= " AND sa.branch_id = :branch_id";
                $params[':branch_id'] = $branch_id;
            }
            
            $query .= " ORDER BY bd.branch_name, 
                        CASE 
                            WHEN sa.session = 'BREAKFAST' THEN 1
                            WHEN sa.session = 'LUNCH' THEN 2
                            WHEN sa.session = 'DINNER' THEN 3
                            ELSE 4
                        END";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Get reservation details for a date
    public function getReservationsByDate($date, $branch_id = null, $session = null) {
        try {
            $query = "
                SELECT rd.*, bd.branch_name, u.username 
                FROM Reservation_details rd
                LEFT JOIN branch_details bd ON rd.branch_name = bd.branch_name
                LEFT JOIN users u ON rd.user_id = u.user_id
                WHERE rd.Reservation_date = :date AND rd.Status != 'cancelled'
            ";
            
            $params = [':date' => $date];
            
            if ($branch_id) {
                $query .= " AND bd.branch_id = :branch_id";
                $params[':branch_id'] = $branch_id;
            }
            
            if ($session) {
                $query .= " AND rd.Session = :session";
                $params[':session'] = strtoupper($session);
            }
            
            $query .= " ORDER BY rd.created_at DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Check availability before reservation
    public function checkAvailability($date, $branch_name, $session, $guests) {
        try {
            // Get branch ID
            $stmt = $this->pdo->prepare("SELECT branch_id FROM branch_details WHERE branch_name = :branch");
            $stmt->execute([':branch' => strtoupper($branch_name)]);
            $branch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$branch) {
                return ['available' => false, 'message' => 'Invalid branch'];
            }
            
            // Check current availability
            $stmt = $this->pdo->prepare("
                SELECT * FROM seat_availability 
                WHERE branch_id = :branch_id AND date = :date AND session = :session
            ");
            $stmt->execute([
                ':branch_id' => $branch['branch_id'],
                ':date' => $date,
                ':session' => strtoupper($session)
            ]);
            
            $availability = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Default total seats if no record exists
            $totalSeats = 100;
            $reservedSeats = 0;
            
            if ($availability) {
                $totalSeats = $availability['total_seats'];
                $reservedSeats = $availability['reserved_seats'];
            }
            
            $availableSeats = $totalSeats - $reservedSeats;
            
            if ($availableSeats >= $guests) {
                return [
                    'available' => true,
                    'available_seats' => $availableSeats,
                    'message' => 'Seats available'
                ];
            } else {
                return [
                    'available' => false,
                    'available_seats' => $availableSeats,
                    'message' => "Only $availableSeats seats available"
                ];
            }
        } catch (PDOException $e) {
            return ['available' => false, 'message' => 'Error checking availability'];
        }
    }
}