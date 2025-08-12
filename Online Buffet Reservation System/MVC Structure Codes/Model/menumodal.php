<?php
// Model/menumodal.php
class MenuModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all menu items by session
    public function getMenuBySession($session = null) {
        try {
            if ($session) {
                $stmt = $this->pdo->prepare("
                    SELECT * FROM buffet_menu 
                    WHERE session = :session AND is_available = TRUE 
                    ORDER BY category, item_name
                ");
                $stmt->execute([':session' => $session]);
            } else {
                $stmt = $this->pdo->query("
                    SELECT * FROM buffet_menu 
                    WHERE is_available = TRUE 
                    ORDER BY session, category, item_name
                ");
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Get menu items grouped by category
    public function getMenuGroupedByCategory($session) {
        $items = $this->getMenuBySession($session);
        $grouped = [];
        
        foreach ($items as $item) {
            $grouped[$item['category']][] = $item;
        }
        
        return $grouped;
    }
    
    // Get pricing information
    public function getPricing() {
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM session_pricing 
                WHERE is_active = TRUE 
                ORDER BY 
                    CASE 
                        WHEN session_name = 'Breakfast' THEN 1
                        WHEN session_name = 'Lunch' THEN 2
                        WHEN session_name = 'Dinner' THEN 3
                        ELSE 4
                    END
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
