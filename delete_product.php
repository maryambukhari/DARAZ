<?php
// delete_product.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='seller_dashboard.php';</script>";
    exit;
}

$product_id = (int)$_GET['id'];
$seller_id = $_SESSION['user_id'];

// Verify product ownership
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->execute([$product_id, $seller_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $error = "Product not found or you don't have permission to delete it.";
    echo "<script>alert('$error'); window.location.href='seller_dashboard.php';</script>";
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Delete from cart first (if any)
        $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ?");
        $stmt->execute([$product_id]);

        // Delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$product_id, $seller_id]);

        // Optionally delete image file
        if ($product['image'] && file_exists($product['image'])) {
            unlink($product['image']);
        }

        echo "<script>alert('Product deleted successfully!'); window.location.href='seller_dashboard.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting product: " . $e->getMessage();
        echo "<script>alert('$error'); window.location.href='seller_dashboard.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - Daraz Clone</title>
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
            min-height: 100vh;
        }

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

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 500px;
            width: 90%;
            margin: 100px auto;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .form-container h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .form-container p {
            text-align: center;
            margin-bottom: 1rem;
        }

        .form-container .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .form-container button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--animation-duration);
        }

        .form-container button.confirm {
            background: #dc3545;
            color: white;
        }

        .form-container button.cancel {
            background: #6c757d;
            color: white;
        }

        .form-container button:hover {
            filter: brightness(90%);
        }

        .form-container button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .form-container button:hover::before {
            left: 100%;
        }

        .error {
            color: red;
            text-align: center;
            background: #ffebee;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }

        @media (max-width: 480px) {
            .form-container {
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
            <a href="#" onclick="window.location.href='seller_dashboard.php'">Dashboard</a>
            <a href="#" onclick="window.location.href='logout.php'">Logout</a>
        </div>
    </div>
    <div class="form-container">
        <h2>Delete Product</h2>
        <p>Are you sure you want to delete "<strong><?php echo htmlspecialchars($product['name']); ?></strong>"? This action cannot be undone.</p>
        <form method="POST">
            <div class="buttons">
                <button type="submit" class="confirm">Confirm Delete</button>
                <button type="button" class="cancel" onclick="window.location.href='seller_dashboard.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
