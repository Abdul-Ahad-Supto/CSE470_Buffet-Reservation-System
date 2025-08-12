<?php
// Controller/authcontroller.php
session_start();
require_once '../Model/pdov2.php';
require_once '../Model/usermodal.php';

$userModel = new UserModel($pdo);
$action = $_POST['action'] ?? $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'register':
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
        
        $result = $userModel->register($data);
        echo json_encode($result);
        break;
        
    case 'login':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required']);
            exit;
        }
        
        $result = $userModel->login($username, $password);
        echo json_encode($result);
        break;
        
    case 'verify_otp':
        $user_id = $_POST['user_id'] ?? '';
        $otp = $_POST['otp'] ?? '';
        
        if (empty($user_id) || empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        $result = $userModel->verifyOTP($user_id, $otp);
        echo json_encode($result);
        break;
        
    case 'resend_otp':
        $user_id = $_POST['user_id'] ?? '';
        
        if (empty($user_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        $result = $userModel->resendOTP($user_id);
        echo json_encode($result);
        break;
        
    case 'logout':
        $result = $userModel->logout();
        header('Location: ../View/login.php');
        exit;
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>