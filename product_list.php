<?php
// product_list.php
session_start();
include 'db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}
if ($min_price) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Daraz Clone</title>
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
        .filter-container {
            padding: 20px;
            background: white;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .filter-container form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-container input, .filter-container select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .filter-container button {
            padding: 10px 20px;
            background: #f57224;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .filter-container button:hover {
            background: #e55e1f;
        }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-10px);
        }
        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .product-card h3 {
            margin: 10px;
            font-size: 18px;
        }
        .product-card p {
            margin: 0 10px 10px;
            color: #f57224;
        }
        @media (max-width: 600px) {
            .products {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" style="color: white;">Home</a> |
        <a href="#" onclick="window.location.href='cart.php'" style="color: white;">Cart</a> |
        <a href="#" onclick="window.location.href='logout.php'" style="color: white;">Logout</a>
    </div>
    <div class="filter-container">
        <form method="GET">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Electronics" <?php if ($category == 'Electronics') echo 'selected'; ?>>Electronics</option>
                <option value="Fashion" <?php if ($category == 'Fashion') echo 'selected'; ?>>Fashion</option>
                <option value="Home & Kitchen" <?php if ($category == 'Home & Kitchen') echo 'selected'; ?>>Home & Kitchen</option>
            </select>
            <input type="number" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($min_price); ?>">
            <input type="number" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($max_price); ?>">
            <button type="submit">Filter</button>
        </form>
    </div>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/150'; ?>" alt="<?php echo $product['name']; ?>">
                <h3><?php echo $product['name']; ?></h3>
                <p>PKR <?php echo number_format($product['price'], 2); ?></p>
                <button onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">View Details</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
