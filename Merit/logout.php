<?php
session_start();

// Store user info for logout message before destroying session
$user_name = isset($_SESSION['Name']) ? $_SESSION['Name'] : 'User';
$user_role = isset($_SESSION['Role']) ? $_SESSION['Role'] : 'user';

// Clear all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a new session for the logout message
session_start();
$_SESSION['logout_message'] = "You have been successfully logged out. Thank you for using MyPetakom!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - MyPetakom</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .logout-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logout-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .logout-title {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        
        .logout-message {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .user-info {
            background: #f0f9ff;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid #0ea5e9;
        }
        
        .user-info p {
            color: #0c4a6e;
            font-weight: 600;
        }
        
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: rgba(107, 114, 128, 0.1);
            color: #374151;
            border: 2px solid #e5e7eb;
        }
        
        .btn-secondary:hover {
            background: rgba(107, 114, 128, 0.2);
            border-color: #d1d5db;
        }
        
        .security-note {
            margin-top: 2rem;
            padding: 1rem;
            background: #fef3c7;
            border-radius: 0.5rem;
            border-left: 4px solid #f59e0b;
        }
        
        .security-note p {
            font-size: 0.9rem;
            color: #92400e;
            margin: 0;
        }
        
        .countdown {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        @media (max-width: 480px) {
            .logout-container {
                padding: 2rem 1.5rem;
            }
            
            .buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">👋</div>
        
        <h1 class="logout-title">Successfully Logged Out</h1>
        
        <div class="user-info">
            <p>Goodbye, <?= htmlspecialchars($user_name) ?>!</p>
        </div>
        
        <p class="logout-message">
            You have been safely logged out of your MyPetakom account. 
            All your session data has been cleared for security purposes.
        </p>
        
        <div class="buttons">
            <a href="login.php" class="btn btn-primary">
                🔐 Login Again
            </a>
            <a href="index.php" class="btn btn-secondary">
                🏠 Go to Homepage
            </a>
        </div>
        
        <div class="security-note">
            <p>
                <strong>Security Tip:</strong> For your protection, please close all browser windows 
                if you're using a shared computer.
            </p>
        </div>
        
        <div class="countdown">
            <p id="redirect-message">You will be redirected to the login page in <span id="countdown">10</span> seconds...</p>
        </div>
    </div>

    <script>
        // Auto-redirect countdown
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        const redirectMessage = document.getElementById('redirect-message');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                redirectMessage.textContent = 'Redirecting to login page...';
                window.location.href = 'login.php';
            }
        }, 1000);
        
        // Clear any remaining timers when page unloads
        window.addEventListener('beforeunload', () => {
            clearInterval(timer);
        });
        
        // Add click event to stop auto-redirect when user interacts with buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', () => {
                clearInterval(timer);
                redirectMessage.style.display = 'none';
            });
        });
    </script>
</body>
</html>