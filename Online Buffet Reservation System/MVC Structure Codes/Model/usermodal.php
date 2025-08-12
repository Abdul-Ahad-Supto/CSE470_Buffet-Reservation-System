<?php
// Model/usermodal.php
// User authentication and management model

class UserModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Register new user
    public function register($data) {
        try {
            // Check if email or username already exists
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE email = :email OR username = :username");
            $stmt->execute([
                ':email' => $data['email'],
                ':username' => $data['username']
            ]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email or username already exists'];
            }
            
            // Hash password
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Generate OTP
            $otp = $this->generateOTP();
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password_hash, phone, otp_code, otp_expiry) 
                VALUES (:username, :email, :password_hash, :phone, :otp, :otp_expiry)
            ");
            
            $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password_hash' => $password_hash,
                ':phone' => $data['phone'],
                ':otp' => $otp,
                ':otp_expiry' => $otp_expiry
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Send OTP (in production, use SMS service)
            $this->sendOTP($data['phone'], $otp);
            
            return [
                'success' => true, 
                'user_id' => $user_id,
                'message' => 'Registration successful. Please verify your phone number.'
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    // Login user
    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, username, email, password_hash, phone_verified, role 
                FROM users 
                WHERE (username = :username OR email = :username) AND is_active = TRUE
            ");
            $stmt->execute([':username' => $username]);
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            if (!$user['phone_verified']) {
                return ['success' => false, 'message' => 'Please verify your phone number first', 'user_id' => $user['user_id']];
            }
            
            // Update last login
            $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user['user_id']]);
            
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    // Verify OTP
    public function verifyOTP($user_id, $otp) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT otp_code, otp_expiry 
                FROM users 
                WHERE user_id = :user_id AND phone_verified = FALSE
            ");
            $stmt->execute([':user_id' => $user_id]);
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Invalid request or already verified'];
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user['otp_code'] !== $otp) {
                return ['success' => false, 'message' => 'Invalid OTP'];
            }
            
            if (strtotime($user['otp_expiry']) < time()) {
                return ['success' => false, 'message' => 'OTP expired. Please request a new one.'];
            }
            
            // Mark phone as verified
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET phone_verified = TRUE, otp_code = NULL, otp_expiry = NULL 
                WHERE user_id = :user_id
            ");
            $stmt->execute([':user_id' => $user_id]);
            
            // Log verification
            $this->logOTPVerification($user_id, $otp, true);
            
            return ['success' => true, 'message' => 'Phone number verified successfully'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()];
        }
    }
    
    // Resend OTP
    public function resendOTP($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT phone FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $otp = $this->generateOTP();
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET otp_code = :otp, otp_expiry = :otp_expiry 
                WHERE user_id = :user_id
            ");
            $stmt->execute([
                ':otp' => $otp,
                ':otp_expiry' => $otp_expiry,
                ':user_id' => $user_id
            ]);
            
            $this->sendOTP($user['phone'], $otp);
            
            return ['success' => true, 'message' => 'OTP sent successfully'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to resend OTP: ' . $e->getMessage()];
        }
    }
    
    // Generate OTP
    private function generateOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    // Send OTP (placeholder - implement actual SMS service)
    private function sendOTP($phone, $otp) {
        // In production, integrate with SMS service like Twilio, Nexmo, etc.
        // For testing, you can:
        // 1. Log to file
        file_put_contents('../logs/otp.log', 
            date('Y-m-d H:i:s') . " - Phone: $phone, OTP: $otp\n", 
            FILE_APPEND
        );
        
        // 2. Store in session for testing
        $_SESSION['test_otp'] = $otp;
        
        // 3. In production, use SMS API:
        // $this->sendSMS($phone, "Your verification code is: $otp");
    }
    
    // Log OTP verification
    private function logOTPVerification($user_id, $otp, $verified) {
        $stmt = $this->pdo->prepare("
            SELECT phone FROM users WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO otp_verification_log (phone, otp_code, is_verified, verified_at, ip_address)
            VALUES (:phone, :otp, :verified, :verified_at, :ip)
        ");
        $stmt->execute([
            ':phone' => $user['phone'],
            ':otp' => $otp,
            ':verified' => $verified,
            ':verified_at' => $verified ? date('Y-m-d H:i:s') : null,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Check if user is admin
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    // Get user by ID
    public function getUserById($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT user_id, username, email, phone, role, created_at, last_login 
            FROM users 
            WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
