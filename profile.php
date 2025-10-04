<?php
// profile.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $user_id]);
    echo "<script>window.location.href='profile.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Daraz Clone</title>
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

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, var(--primary), #e55e1f);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            z-index: 1000;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            position: relative;
            transition: var(--animation-duration);
        }

        .navbar a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background: var(--secondary);
            transition: width var(--animation-duration);
        }

        .navbar a:hover::after {
            width: 100%;
        }

        /* Profile Container with Glassmorphism */
        .profile-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 500px;
            width: 90%;
            margin-top: 80px;
            animation: slideInProfile 0.5s ease-out;
        }

        @keyframes slideInProfile {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .profile-container h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .profile-container label {
            display: block;
            margin: 0.5rem 0;
            font-weight: 500;
        }

        .profile-container input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.8);
            transition: var(--animation-duration);
            position: relative;
        }

        .profile-container input:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary);
            transform: scale(1.02);
        }

        .profile-container input::before {
            content: attr(placeholder);
            position: absolute;
            top: -0.5rem;
            left: 1rem;
            font-size: 0.8rem;
            color: var(--primary);
            transition: var(--animation-duration);
            opacity: 0;
        }

        .profile-container input:focus::before {
            opacity: 1;
        }

        .profile-container button {
            width: 100%;
            padding: 0.8rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--animation-duration);
        }

        .profile-container button:hover {
            background: #e55e1f;
        }

        .profile-container button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .profile-container button:hover::before {
            left: 100%;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .profile-container {
                padding: 1rem;
            }
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Daraz Clone</a>
        </div>
        <div>
            <a href="#" onclick="window.location.href='cart.php'">Cart</a>
            <?php if ($_SESSION['role'] == 'seller'): ?>
                <a href="#" onclick="window.location.href='seller_dashboard.php'">Seller Dashboard</a>
            <?php endif; ?>
            <a href="#" onclick="window.location.href='logout.php'">Logout</a>
        </div>
    </div>
    <div class="profile-container">
        <h2>Your Profile</h2>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label>Role</label>
            <input type="text" value="<?php echo $user['role']; ?>" disabled>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
