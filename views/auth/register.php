<?php
// views/auth/register.php
if (!isset($userType)) {
    $userType = $_GET['type'] ?? 'buyer';
}
?>
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
            max-width: 500px;
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        <h1 class="auth-title">Create Account</h1>
        
        <?php if ($error = \Session::getFlash('error')): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
    <form method="POST" action="/auth/register">
            <div class="form-group">
                <label for="user_type">Account Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="buyer" <?php echo $userType === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                    <option value="seller" <?php echo $userType === 'seller' ? 'selected' : ''; ?>>Seller</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Create Account</button>
            
            <div class="auth-links">
                <p>Already have an account? <a href="/login">Login here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
