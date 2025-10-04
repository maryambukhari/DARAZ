<?php
// cart.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.id, c.quantity, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['remove_id'], $user_id]);
    echo "<script>window.location.href='cart.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Daraz Clone</title>
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
        .cart-container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .cart-table th, .cart-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .cart-table th {
            background: #f57224;
            color: white;
        }
        .cart-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-remove { background: #f44336; color: white; }
        .btn-remove:hover { background: #e53935; }
        .btn-checkout {
            background: #f57224;
            color: white;
            padding: 10px 20px;
            margin: 20px 0;
            display: inline-block;
        }
        .btn-checkout:hover { background: #e55e1f; }
        @media (max-width: 600px) {
            .cart-table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" style="color: white;">Home</a> |
        <a href="#" onclick="window.location.href='checkout.php'" style="color: white;">Checkout</a> |
        <a href="#" onclick="window.location.href='logout.php'" style="color: white;">Logout</a>
    </div>
    <div class="cart-container">
        <h2>Your Cart</h2>
        <table class="cart-table">
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php $total = 0; foreach ($cart_items as $item): ?>
                <tr>
                    <td><img src="<?php echo $item['image'] ?: 'https://via.placeholder.com/50'; ?>" alt="<?php echo $item['name']; ?>"></td>
                    <td><?php echo $item['name']; ?></td>
                    <td>PKR <?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>PKR <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="remove_id" value="<?php echo $item['id']; ?>">
                            <button class="btn btn-remove" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                <td colspan="2">PKR <?php echo number_format($total, 2); ?></td>
            </tr>
        </table>
        <a href="#" onclick="window.location.href='checkout.php'" class="btn-checkout">Proceed to Checkout</a>
    </div>
</body>
</html>
