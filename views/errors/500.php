<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Something Went Wrong</title>
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
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
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

        .error-details {
            margin-top: 2rem;
            font-size: 0.875rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">😔</div>
            <div class="error-code">500</div>
            <div class="error-title">Oops! Something Went Wrong</div>
            <div class="error-message">
                We're sorry, but something unexpected happened on our end. 
                Our team has been notified and we're working to fix it as quickly as possible.
            </div>
            <a href="<?php echo url('/'); ?>" class="error-link">Take Me Home</a>
            <div class="error-details">
                Try refreshing the page or come back in a few minutes.
            </div>
        </div>
    </div>
</body>
</html>