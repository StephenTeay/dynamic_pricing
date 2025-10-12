<?php
// views/auth/login.php
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .auth-container {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .auth-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #0f172a;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #0f172a;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #1d4ed8;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .auth-links a {
            color: #2563eb;
            text-decoration: none;
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Login</h1>
        
        <?php if ($error = \Session::getFlash('error')): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
    <form method="POST" action="/auth/login">
            <div class="form-group">
                <label for="identifier">Email or Username</label>
                <input type="text" id="identifier" name="identifier" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-submit">Login</button>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="/register">Register here</a></p>
                <p><a href="/forgot-password">Forgot password?</a></p>
            </div>
        </form>
    </div>
</body>
</html>
