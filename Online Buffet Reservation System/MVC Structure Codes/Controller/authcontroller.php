<?php
// Controller/authcontroller.php (Debug Version)
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Add error logging
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php_errors.log');

try {
    require_once '../Model/pdov2.php';
    require_once '../Model/usermodal.php';

    $userModel = new UserModel($pdo);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    // Log the incoming request
    error_log("Auth request: action=$action, data=" . json_encode($_POST));

    header('Content-Type: application/json');

    switch ($action) {
        case 'register':
            try {
                $data = [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'password' => $_POST['password'] ?? ''
                ];
                
                // Validate input
                if (empty($data['username']) || empty($data['email']) || empty($data['phone']) || empty($data['password'])) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                // Additional validation
                if (strlen($data['password']) < 6) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                    exit;
                }
                
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    exit;
                }
                
                $result = $userModel->register($data);
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
            }
            break;
            
        case 'login':
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
                    exit;
                }
                
                $result = $userModel->login($username, $password);
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Login failed: ' . $e->getMessage()]);
            }
            break;
            
        case 'verify_otp':
            try {
                $user_id = $_POST['user_id'] ?? '';
                $otp = $_POST['otp'] ?? '';
                
                if (empty($user_id) || empty($otp)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                    exit;
                }
                
                $result = $userModel->verifyOTP($user_id, $otp);
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("OTP verification error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()]);
            }
            break;
            
        case 'resend_otp':
            try {
                $user_id = $_POST['user_id'] ?? '';
                
                if (empty($user_id)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                    exit;
                }
                
                $result = $userModel->resendOTP($user_id);
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("Resend OTP error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Failed to resend OTP: ' . $e->getMessage()]);
            }
            break;
            
        case 'logout':
            $result = $userModel->logout();
            header('Location: ../View/login.php');
            exit;
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Fatal error in authcontroller: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>