<?php
// order_history.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT o.id, o.total, o.status, o.created_at FROM orders o WHERE o.user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Daraz Clone</title>
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
        .order-container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .order-table th, .order-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .order-table th {
            background: #f57224;
            color: white;
        }
        .order-table tr:hover {
            background: #f9f9f9;
            transition: background 0.3s;
        }
        @media (max-width: 600px) {
            .order-table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" style="color: white;">Home</a> |
        <a href="#" onclick="window.location.href='cart.php'" style="color: white;">Cart</a> |
        <a href="#" onclick="window.location.href='logout.php'" style="color: white;">Logout</a>
    </div>
    <div class="order-container">
        <h2>Your Orders</h2>
        <table class="order-table">
            <tr>
                <th>Order ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td>PKR <?php echo number_format($order['total'], 2); ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
