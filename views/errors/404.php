<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
        }
        
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        
        .error-content {
            text-align: center;
            color: white;
            max-width: 600px;
        }
        
        .error-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: swing 2s ease-in-out infinite;
        }

        @keyframes swing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(10deg); }
            75% { transform: rotate(-10deg); }
        }
        
        .error-code {
            font-size: 5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.8;
        }
        
        .error-link {
            display: inline-block;
            padding: 1rem 2.5rem;
            background-color: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .error-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .error-suggestions {
            margin-top: 2.5rem;
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .error-suggestions p {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">🔍</div>
            <div class="error-code">404</div>
            <div class="error-title">Page Not Found</div>
            <div class="error-message">
                We've searched high and low, but we couldn't find the page you're looking for. 
                It might have been moved, deleted, or perhaps it never existed.
            </div>
            <a href="<?php echo BASE_URL; ?>" class="error-link">Take Me Home</a>
            <div class="error-suggestions">
                <p>💡 Check the URL for typos or try searching from our homepage.</p>
            </div>
        </div>
    </div>
</body>
</html>