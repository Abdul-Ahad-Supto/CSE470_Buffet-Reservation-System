<?php
// Model/menumodal.php - Enhanced version with AI recommendations
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
            error_log("MenuModel::getMenuBySession error: " . $e->getMessage());
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
            error_log("MenuModel::getPricing error: " . $e->getMessage());
            return [];
        }
    }
    
    // AI-based recommendations for users
    public function getUserRecommendations($user_id) {
        try {
            $recommendations = [];
            
            // Get user's previous reservations and preferences
            $userHistory = $this->getUserDiningHistory($user_id);
            
            if (empty($userHistory)) {
                // New user - return popular items
                return $this->getPopularItemRecommendations();
            }
            
            // Analyze user preferences
            $preferences = $this->analyzeUserPreferences($userHistory);
            
            // Generate personalized recommendations
            $recommendations = $this->generatePersonalizedRecommendations($preferences);
            
            return $recommendations;
            
        } catch (PDOException $e) {
            error_log("MenuModel::getUserRecommendations error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get user's dining history
    private function getUserDiningHistory($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT rd.*, ul.menu_item, ul.session as preferred_session
                FROM Reservation_details rd
                LEFT JOIN user_activity_log ul ON rd.user_id = ul.user_id
                WHERE rd.user_id = :user_id 
                AND rd.Status = 'confirm'
                AND rd.Reservation_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                ORDER BY rd.created_at DESC
                LIMIT 20
            ");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Analyze user preferences from history
    private function analyzeUserPreferences($history) {
        $preferences = [
            'dietary' => 'mixed', // vegetarian, non-vegetarian, mixed
            'cuisine_preference' => [],
            'spice_tolerance' => 'mild',
            'favorite_sessions' => [],
            'group_size_preference' => 2
        ];
        
        $vegCount = 0;
        $nonVegCount = 0;
        $sessions = [];
        $totalReservations = count($history);
        
        foreach ($history as $reservation) {
            // Analyze sessions
            if (!empty($reservation['Session'])) {
                $sessions[] = $reservation['Session'];
            }
            
            // Estimate dietary preference based on session patterns
            // (This is simplified - in real implementation, you'd track actual menu choices)
            if ($reservation['Session'] === 'Breakfast') {
                $vegCount += 0.7; // Breakfast tends to be more vegetarian
            } elseif (in_array($reservation['Session'], ['Lunch', 'Dinner'])) {
                $nonVegCount += 0.6;
                $vegCount += 0.4;
            }
        }
        
        // Determine dietary preference
        if ($vegCount > $nonVegCount * 1.5) {
            $preferences['dietary'] = 'vegetarian';
        } elseif ($nonVegCount > $vegCount * 1.5) {
            $preferences['dietary'] = 'non-vegetarian';
        }
        
        // Analyze favorite sessions
        $preferences['favorite_sessions'] = array_count_values($sessions);
        arsort($preferences['favorite_sessions']);
        
        return $preferences;
    }
    
    // Generate personalized recommendations
    private function generatePersonalizedRecommendations($preferences) {
        try {
            $recommendations = [];
            
            // Base query for recommendations
            $whereConditions = ["is_available = TRUE"];
            $params = [];
            
            // Filter by dietary preference
            if ($preferences['dietary'] === 'vegetarian') {
                $whereConditions[] = "is_vegetarian = TRUE";
            } elseif ($preferences['dietary'] === 'non-vegetarian') {
                $whereConditions[] = "is_vegetarian = FALSE";
            }
            
            // Get top session preference
            $topSession = key($preferences['favorite_sessions']);
            if ($topSession) {
                $whereConditions[] = "session = :session";
                $params[':session'] = $topSession;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->pdo->prepare("
                SELECT *, 
                CASE 
                    WHEN cuisine_type IN ('North Indian', 'South Indian', 'Bengali') THEN 5
                    WHEN cuisine_type IN ('Continental', 'Italian') THEN 4
                    WHEN cuisine_type IN ('Chinese', 'Thai') THEN 3
                    ELSE 2
                END as popularity_score
                FROM buffet_menu 
                WHERE $whereClause
                ORDER BY popularity_score DESC, RAND()
                LIMIT 5
            ");
            
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                $reason = $this->generateRecommendationReason($item, $preferences);
                $recommendations[] = [
                    'item_name' => $item['item_name'],
                    'reason' => $reason,
                    'category' => $item['category'],
                    'session' => $item['session']
                ];
            }
            
            return $recommendations;
            
        } catch (PDOException $e) {
            error_log("MenuModel::generatePersonalizedRecommendations error: " . $e->getMessage());
            return [];
        }
    }
    
    // Generate recommendation reason
    private function generateRecommendationReason($item, $preferences) {
        $reasons = [];
        
        if ($preferences['dietary'] === 'vegetarian' && $item['is_vegetarian']) {
            $reasons[] = "Perfect for vegetarians";
        } elseif ($preferences['dietary'] === 'non-vegetarian' && !$item['is_vegetarian']) {
            $reasons[] = "Great protein choice";
        }
        
        if (!empty($preferences['favorite_sessions']) && $item['session'] === key($preferences['favorite_sessions'])) {
            $reasons[] = "Matches your preferred dining time";
        }
        
        // Cuisine-based reasons
        $cuisineReasons = [
            'North Indian' => "Rich and flavorful",
            'South Indian' => "Authentic Southern flavors", 
            'Continental' => "International favorite",
            'Italian' => "Classic comfort food",
            'Chinese' => "Popular Asian choice",
            'Bengali' => "Traditional Bengali taste"
        ];
        
        if (isset($cuisineReasons[$item['cuisine_type']])) {
            $reasons[] = $cuisineReasons[$item['cuisine_type']];
        }
        
        // Spice level consideration
        if ($item['spice_level'] === 'Mild') {
            $reasons[] = "Mild and gentle flavors";
        } elseif ($item['spice_level'] === 'Medium') {
            $reasons[] = "Perfect spice balance";
        }
        
        return !empty($reasons) ? implode(', ', array_slice($reasons, 0, 2)) : "Highly recommended";
    }
    
    // Get popular items for new users
    private function getPopularItemRecommendations() {
        try {
            $stmt = $this->pdo->query("
                SELECT item_name, category, session,
                CASE 
                    WHEN item_name IN ('Butter Chicken', 'Palak Paneer', 'Vegetable Biryani', 'Gulab Jamun') THEN 'Customer favorite'
                    WHEN cuisine_type = 'North Indian' THEN 'Popular Indian cuisine'
                    WHEN is_vegetarian = TRUE THEN 'Vegetarian delight'
                    ELSE 'Chef recommended'
                END as reason
                FROM buffet_menu 
                WHERE is_available = TRUE 
                AND item_name IN ('Butter Chicken', 'Palak Paneer', 'Vegetable Biryani', 'Gulab Jamun', 'Dal Tadka', 'Tandoori Chicken')
                ORDER BY 
                CASE 
                    WHEN item_name = 'Butter Chicken' THEN 1
                    WHEN item_name = 'Palak Paneer' THEN 2
                    WHEN item_name = 'Vegetable Biryani' THEN 3
                    ELSE 4
                END
                LIMIT 4
            ");
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                ['item_name' => 'Butter Chicken', 'reason' => 'Most popular dish', 'category' => 'Main Course', 'session' => 'Lunch'],
                ['item_name' => 'Palak Paneer', 'reason' => 'Vegetarian favorite', 'category' => 'Main Course', 'session' => 'Lunch'],
                ['item_name' => 'Vegetable Biryani', 'reason' => 'Aromatic rice dish', 'category' => 'Main Course', 'session' => 'Lunch']
            ];
        }
    }
    
    // Log user menu preferences (call this when user makes a reservation)
    public function logUserMenuPreference($user_id, $session, $dietary_preference = null) {
        try {
            // Create user_menu_preferences table if it doesn't exist
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS user_menu_preferences (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    session VARCHAR(20),
                    dietary_preference ENUM('vegetarian', 'non-vegetarian', 'mixed') DEFAULT 'mixed',
                    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX(user_id)
                )
            ");
            
            $stmt = $this->pdo->prepare("
                INSERT INTO user_menu_preferences (user_id, session, dietary_preference)
                VALUES (:user_id, :session, :dietary_preference)
            ");
            
            $stmt->execute([
                ':user_id' => $user_id,
                ':session' => $session,
                ':dietary_preference' => $dietary_preference ?: 'mixed'
            ]);
            
        } catch (PDOException $e) {
            error_log("MenuModel::logUserMenuPreference error: " . $e->getMessage());
        }
    }
}
?>