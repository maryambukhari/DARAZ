<?php
// login.php
session_start();
include 'db.php';

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Trim to remove spaces
    $password = $_POST['password']; // Plaintext password from form

    // Debug: Echo inputs (remove in production)
    echo "<!-- Debug: Username: '$username', Password length: " . strlen($password) . " -->";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug: Check if user found
    if (!$user) {
        echo "<!-- Debug: No user found for username: $username -->";
        $error = "Invalid credentials! (User not found)";
    } else {
        echo "<!-- Debug: User found. Hash length: " . strlen($user['password']) . ", Hash preview: " . substr($user['password'], 0, 20) . " -->";

        if (password_verify($password, $user['password'])) {
            echo "<!-- Debug: Password verified successfully -->";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo "<script>window.location.href='index.php';</script>";
            exit; // Stop execution after redirect
        } else {
            echo "<!-- Debug: Password verification failed -->";
            $error = "Invalid credentials! (Password mismatch)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daraz Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            width: 350px;
            animation: slideIn 0.5s;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .login-container h2 {
            text-align: center;
            color: #f57224;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #f57224;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-container button:hover {
            background: #e55e1f;
        }
        .error {
            color: red;
            text-align: center;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        /* Hide debug in production by commenting out */
        .debug { display: none; } /* Add class="debug" to echoes if needed */
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="window.location.href='signup.php'">Signup</a></p>
    </div>
</body>
</html>
