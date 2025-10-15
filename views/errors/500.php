<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .error-content {
            text-align: center;
            color: white;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .error-link {
            display: inline-block;
            padding: 0.75rem 2rem;
            background-color: white;
            color: #2563eb;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .error-link:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-code">500</div>
            <div class="error-message">Internal Server Error</div>
            <p style="margin-bottom: 2rem;">Ouch!</p>
            <a href="<?php echo url('/'); ?>" class="error-link">Go Home</a>
        </div>
    </div>
</body>
</html>
