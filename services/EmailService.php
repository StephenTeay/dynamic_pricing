<?php
// services/EmailService.php

class EmailService {
    /**
     * Send email
     */
    public static function send($to, $subject, $message, $headers = []) {
        if (!ENABLE_EMAIL_NOTIFICATIONS) {
            return false;
        }
        
        $defaultHeaders = [
            'From' => SMTP_FROM_EMAIL,
            'Reply-To' => SMTP_FROM_EMAIL,
            'Content-Type' => 'text/html; charset=UTF-8'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        $headerString = '';
        
        foreach ($headers as $key => $value) {
            $headerString .= "$key: $value\r\n";
        }
        
        return mail($to, $subject, $message, $headerString);
    }
    
    /**
     * Send order confirmation
     */
    public static function sendOrderConfirmation($order, $email) {
        $subject = "Order Confirmation - Order #" . $order['order_number'];
        $message = "
            <h2>Order Confirmation</h2>
            <p>Thank you for your order!</p>
            <p><strong>Order Number:</strong> " . htmlspecialchars($order['order_number']) . "</p>
            <p><strong>Total Amount:</strong> ₦" . number_format($order['total_amount'], 2) . "</p>
            <p>We will notify you when your order is shipped.</p>
        ";
        
        return self::send($email, $subject, $message);
    }
    
    /**
     * Send password reset email
     */
    public static function sendPasswordReset($email, $resetLink) {
        $subject = "Password Reset Request";
        $message = "
            <h2>Password Reset</h2>
            <p>Click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link expires in 1 hour.</p>
        ";
        
        return self::send($email, $subject, $message);
    }
}
?>
