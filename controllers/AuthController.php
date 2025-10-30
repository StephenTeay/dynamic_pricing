<?php
// controllers/AuthController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/SellerProfile.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Validator.php';

class AuthController extends Controller {
    private $userModel;
    private $sellerModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->sellerModel = new SellerProfile();
        // Share the database connection between models
        $this->userModel->shareConnection($this->sellerModel);
    }

    /**
     * Show registration form
     */
    public function showRegister($params = []) {
        $userType = $_GET['type'] ?? 'buyer';
        if (!in_array($userType, ['buyer', 'seller'])) {
            $userType = 'buyer';
        }
        
        $pageTitle = APP_NAME . ' - Register as ' . ucfirst($userType);
        $errors = Session::getFlash('errors', []);
        $old = Session::getFlash('old', []);
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/register.php';
        // require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showRegisterForm();
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'username' => 'required|min:3|max:50|alphanumeric',
            'email' => 'required|email',
            'full_name' => 'required|min:2|max:255',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|confirmed:password',
            'user_type' => 'required|in:buyer,seller',
            'phone' => 'phone'
        ];
        
        if (!$validator->validate($rules)) {
            Session::setFlash('error', $validator->getFirstError());
            return $this->showRegisterForm($data);
        }
        
        // Check if email or username exists
        if ($this->userModel->emailExists($data['email'])) {
            Session::setFlash('error', 'Email already registered');
            return $this->showRegisterForm($data);
        }
        
        if ($this->userModel->usernameExists($data['username'])) {
            Session::setFlash('error', 'Username already taken');
            return $this->showRegisterForm($data);
        }
        
        try {
            // Log registration attempt
            error_log(sprintf("[%s] Starting registration for user type: %s", 
                date('Y-m-d H:i:s'), 
                $data['user_type']
            ));
            
            $this->userModel->beginTransaction();
            
            // Create user
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'user_type' => $data['user_type'],
                'password' => $data['password']
            ];
            
            $userId = $this->userModel->createUser($userData);
            
            // If seller, create seller profile
            if ($data['user_type'] === 'seller') {
                $sellerData = [
                    'user_id' => $userId,
                    'business_name' => $data['business_name'] ?? $data['full_name'],
                    'business_email' => $data['email'],
                    'business_phone' => $data['phone'] ?? null,
                    'business_description' => $data['business_description'] ?? '',
                    'business_address' => $data['business_address'] ?? ''
                ];
                
                $this->sellerModel->create($sellerData);
            }
            
            $this->userModel->commit();
            
            Session::setFlash('success', 'Registration successful! Please login.');
            redirect('/login');
            exit;
            
        } catch (Exception $e) {
            $this->userModel->rollback();
            
            // Log the detailed error
            $logFile = __DIR__ . '/../logs/error.log';
            $message = sprintf("[%s] Registration failed: %s in %s on line %d\nStack trace: %s\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            error_log($message, 3, $logFile);
            
            Session::setFlash('error', 'Registration failed: ' . $e->getMessage());
            return $this->showRegisterForm($data);
        }
    }
    
    /**
     * Handle user login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showLogin();
        }

        $data = Validator::sanitize($_POST);

        $validator = new Validator($data);
        $rules = [
            'identifier' => 'required',
            'password' => 'required'
        ];

        if (!$validator->validate($rules)) {
            Session::setFlash('error', $validator->getFirstError());
            return $this->showLogin();
        }

        try {
            $user = $this->userModel->verifyLogin($data['identifier'], $data['password']);

            if (!$user) {
                Session::setFlash('error', 'Invalid credentials or account inactive');
                return $this->showLogin();
            }

            // Login user
            Session::login($user['user_id'], $user['user_type'], $user['username']);

            Session::setFlash('success', 'Login successful!');

            // Redirect to appropriate dashboard
            if ($user['user_type'] === 'seller') {
                redirect('/seller/dashboard');
            } else {
                redirect('/buyer/shop');
            }
            exit;
        } catch (Exception $e) {
            // Log the exception to app logs for debugging
            $logFile = __DIR__ . '/../logs/error.log';
            $message = sprintf("[%s] Login exception: %s in %s on line %d\nStack: %s\n",
                date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()
            );
            error_log($message, 3, $logFile);

            // Show a generic error to the user
            Session::setFlash('error', 'An internal error occurred while attempting to log you in. Please try again later.');
            // Also show 500 page when in debug mode
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw $e; // let the global handler show the stack in debug
            }

            return $this->showLogin();
        }
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        Session::logout();
    Session::setFlash('success', 'You have been logged out');
    redirect('/');
    exit;
    }
    
    /**
     * Show login form (public route)
     */
    public function showLogin($params = []) {
        $pageTitle = APP_NAME . ' - Login';
        $errors = Session::getFlash('error', []);
        $old = Session::getFlash('old', []);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/login.php';
        // require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Show register form
     */
    private function showRegisterForm($data = []) {
        include __DIR__ . '/../views/auth/register.php';
    }
    
    /**
     * Handle forgot password
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showForgotPasswordForm();
        }
        
        $email = Validator::sanitize($_POST['email'] ?? '');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address');
            return $this->showForgotPasswordForm();
        }
        
        $token = $this->userModel->createPasswordResetToken($email);
        
        if ($token) {
            // Send reset email
            $resetLink = BASE_URL . '/reset-password?token=' . $token;
            $subject = 'Password Reset Request';
            $body = "Click the link below to reset your password:\n\n$resetLink\n\nThis link expires in 1 hour.";
            
            // In production, use proper email service
            // sendEmail($email, $subject, $body);
            
            Session::setFlash('success', 'Password reset link sent to your email');
        } else {
            Session::setFlash('error', 'Email not found');
        }
        
    redirect('/login');
        exit;
    }
    
    public function showChangePassword() {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please login to change your password');
            redirect('/login');
            exit;
        }
        
        $pageTitle = APP_NAME . ' - Change Password';
        require_once __DIR__ . '/../views/auth/change_password.php';
    }
    
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::isLoggedIn()) {
            redirect('/login');
            exit;
        }
        
        $validator = new Validator($_POST);
        $rules = [
            'current_password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|confirmed:new_password'
        ];
        
        if (!$validator->validate($rules)) {
            Session::setFlash('error', $validator->getFirstError());
            Session::setFlash('old', $_POST);
            redirect('/auth/change-password');
            exit;
        }
        
        $userId = Session::getUserId();
        $user = $this->userModel->find($userId);
        
        if (!$user || !password_verify($_POST['current_password'], $user['password'])) {
            Session::setFlash('error', 'Current password is incorrect');
            redirect('/auth/change-password');
            exit;
        }
        
        $success = $this->userModel->updatePassword($userId, $_POST['new_password']);
        
        if ($success) {
            Session::setFlash('success', 'Password updated successfully');
            redirect('/seller/settings');
        } else {
            Session::setFlash('error', 'Failed to update password');
            redirect('/auth/change-password');
        }
    }
    
    public function notifications() {
        if (!Session::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $notifications = $this->userModel->getNotifications(Session::getUserId());
        
        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => count(array_filter($notifications, fn($n) => !$n['read_at']))
        ]);
        exit;
    }
    
    public function markNotificationRead() {
        if (!Session::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        if (!$notificationId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Notification ID is required']);
            exit;
        }
        
        $success = $this->userModel->markNotificationRead($notificationId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
    
    /**
     * Show forgot password form
     */
    private function showForgotPasswordForm() {
        include __DIR__ . '/../views/auth/forgot_password.php';
    }
    
    /**
     * Handle password reset
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            Session::setFlash('error', 'Invalid reset token');
            redirect('/login');
            exit;
        }
        
        $resetData = $this->userModel->verifyPasswordResetToken($token);
        
        if (!$resetData) {
            Session::setFlash('error', 'Reset token expired or invalid');
            redirect('/forgot-password');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showResetPasswordForm($token);
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'password' => 'required|min:8',
            'password_confirmation' => 'required|confirmed:password'
        ];
        
        if (!$validator->validate($rules)) {
            Session::setFlash('error', $validator->getFirstError());
            return $this->showResetPasswordForm($token);
        }
        
        try {
            // Update password
            $this->userModel->updatePassword($resetData['user_id'], $data['password']);
            
            // Mark token as used
            $this->userModel->markTokenUsed($token);
            
            Session::setFlash('success', 'Password reset successful! Please login with your new password.');
            redirect('/login');
            exit;
            
        } catch (Exception $e) {
            Session::setFlash('error', 'Password reset failed: ' . $e->getMessage());
            return $this->showResetPasswordForm($token);
        }
    }
    
    /**
     * Show reset password form
     */
    private function showResetPasswordForm($token) {
        include __DIR__ . '/../views/auth/reset_password.php';
    }
    
    /**
     * Require user to be logged in
     */
    public static function requireLogin() {
        Session::start();
        
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please login first');
            redirect('/login');
            exit;
        }
    }
    
    /**
     * Require user to be seller
     */
    public static function requireSeller() {
        self::requireLogin();
        
        if (Session::getUserType() !== USER_TYPE_SELLER) {
            Session::setFlash('error', 'Only sellers can access this page');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Require user to be buyer
     */
    public static function requireBuyer() {
        self::requireLogin();
        
        if (Session::getUserType() !== USER_TYPE_BUYER) {
            Session::setFlash('error', 'Only buyers can access this page');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Require user to be admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (Session::getUserType() !== USER_TYPE_ADMIN) {
            Session::setFlash('error', 'Only administrators can access this page');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Require guest (not logged in)
     */
    public static function requireGuest() {
        Session::start();
        
        if (Session::isLoggedIn()) {
            if (Session::isSeller()) {
                redirect('/seller/dashboard');
            } else {
                redirect('/buyer/shop');
            }
            exit;
        }
    }
}