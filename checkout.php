<?php
// checkout.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.id, c.quantity, c.product_id, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Pending')");
    $stmt->execute([$user_id, $total]);
    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo "<script>window.location.href='order_history.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Daraz Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }
        .navbar {
            background: #f57224;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .checkout-container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: slideIn 0.5s;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .checkout-container h2 {
            color: #f57224;
        }
        .checkout-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .checkout-container button {
            width: 100%;
            padding: 10px;
            background: #f57224;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .checkout-container button:hover {
            background: #e55e1f;
        }
        .summary {
            margin: 20px 0;
        }
        .summary p {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" style="color: white;">Home</a> |
        <a href="#" onclick="window.location.href='cart.php'" style="color: white;">Cart</a> |
        <a href="#" onclick="window.location.href='logout.php'" style="color: white;">Logout</a>
    </div>
    <div class="checkout-container">
        <h2>Checkout</h2>
        <div class="summary">
            <?php $total = 0; foreach ($cart_items as $item): ?>
                <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
            <p><strong>Total: </strong>PKR <?php echo number_format($total, 2); ?></p>
        </div>
        <form method="POST">
            <input type="text" placeholder="Card Number (Dummy)" required>
            <input type="text" placeholder="Cardholder Name" required>
            <input type="text" placeholder="Expiry Date (MM/YY)" required>
            <input type="text" placeholder="CVV" required>
            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>
