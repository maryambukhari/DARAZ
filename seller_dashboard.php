<?php
// seller_dashboard.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$seller_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ?");
$stmt->execute([$seller_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Daraz Clone</title>
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
        .dashboard {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .dashboard h2 {
            color: #f57224;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-table th, .product-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .product-table th {
            background: #f57224;
            color: white;
        }
        .product-table tr:hover {
            background: #f9f9f9;
            transition: background 0.3s;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-edit { background: #4caf50; color: white; }
        .btn-edit:hover { background: #45a049; }
        .btn-delete { background: #f44336; color: white; }
        .btn-delete:hover { background: #e53935; }
        .btn-add {
            background: #f57224;
            color: white;
            padding: 10px 20px;
            margin: 20px 0;
            display: inline-block;
        }
        .btn-add:hover { background: #e55e1f; }
        @media (max-width: 600px) {
            .product-table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" style="color: white;">Home</a> |
        <a href="#" onclick="window.location.href='add_product.php'" style="color: white;">Add Product</a> |
        <a href="#" onclick="window.location.href='seller_orders.php'" style="color: white;">Orders</a> |
        <a href="#" onclick="window.location.href='logout.php'" style="color: white;">Logout</a>
    </div>
    <div class="dashboard">
        <h2>Seller Dashboard</h2>
        <a href="#" onclick="window.location.href='add_product.php'" class="btn-add">Add New Product</a>
        <table class="product-table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td>PKR <?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="window.location.href='edit_product.php?id=<?php echo $product['id']; ?>'">Edit</button>
                        <button class="btn btn-delete" onclick="if(confirm('Are you sure?')) window.location.href='delete_product.php?id=<?php echo $product['id']; ?>'">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
