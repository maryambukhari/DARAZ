<?php
// logout.php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Daraz Clone</title>
    <style>
        :root {
            --primary: #f57224;
            --secondary: #ffe0b2;
            --bg: #f4f4f4;
            --text: #333;
            --shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            --animation-duration: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Logout Container with Loading Animation */
        .logout-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            animation: fadeInLogout 0.5s ease-out;
        }

        @keyframes fadeInLogout {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }

        .logout-container h2 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .logout-container .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--secondary);
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logout-container p {
            margin-bottom: 1rem;
        }

        .logout-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: var(--animation-duration);
        }

        .logout-container a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: var(--primary);
            transition: width var(--animation-duration);
        }

        .logout-container a:hover::after {
            width: 100%;
        }

        /* Redirect Animation */
        .logout-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(245, 114, 36, 0.3), transparent);
            animation: slideRedirect 2s forwards;
        }

        @keyframes slideRedirect {
            100% { left: 100%; }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .logout-container {
                padding: 1rem;
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2>Logging Out</h2>
        <div class="spinner"></div>
        <p>You have been logged out successfully.</p>
        <p><a href="#" onclick="window.location.href='login.php'">Go to Login</a></p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    </script>
</body>
</html>
