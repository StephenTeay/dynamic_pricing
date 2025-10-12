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
        require_once __DIR__ . '/../views/layouts/footer.php';
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
            header('Location: /login');
            exit;
            
        } catch (Exception $e) {
            $this->userModel->rollback();
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
            header('Location: /seller/dashboard');
        } else {
            header('Location: /buyer/shop');
        }
        exit;
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        Session::logout();
        Session::setFlash('success', 'You have been logged out');
        header('Location: /');
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
        require_once __DIR__ . '/../views/layouts/footer.php';
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
        
    header('Location: /login');
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
            header('Location: /login');
            exit;
        }
        
        $resetData = $this->userModel->verifyPasswordResetToken($token);
        
        if (!$resetData) {
            Session::setFlash('error', 'Reset token expired or invalid');
            header('Location: /forgot-password');
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
            header('Location: /login');
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
            header('Location: /login.php');
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
            header('Location: /');
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
            header('Location: /');
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
            header('Location: /');
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
                header('Location: /seller/dashboard.php');
            } else {
                header('Location: /buyer/shop.php');
            }
            exit;
        }
    }
}