<?php
// index.php
session_start();
include 'db.php';

// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base path for images (adjust if project is in a subdirectory)
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/daraz_clone/';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $quantity > 0 && $quantity <= $product['stock']) {
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            $new_quantity = $cart_item['quantity'] + $quantity;
            if ($new_quantity <= $product['stock']) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$new_quantity, $user_id, $product_id]);
            } else {
                $error = "Cannot add more than available stock!";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        if (!isset($error)) {
            echo "<script>window.location.href='cart.php';</script>";
            exit;
        }
    } else {
        $error = "Invalid quantity or product not found!";
    }
}

// Debug: Log product and image details
$debug_output = "<!-- Debug: Products fetched: " . count($products) . " -->\n";
foreach ($products as $product) {
    $image_path = $product['image'] ? $product['image'] : 'https://via.placeholder.com/150?text=No+Image';
    if ($product['image'] && strpos($product['image'], 'http') !== 0) {
        $image_path = $base_url . $product['image'];
    }
    $file_exists = $product['image'] && strpos($product['image'], 'http') !== 0 && file_exists($product['image']) ? 'Yes' : 'No';
    $debug_output .= "<!-- Debug: Product ID: {$product['id']}, Name: {$product['name']}, Image: {$image_path}, Exists: {$file_exists} -->\n";
}
echo $debug_output;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="cache-control" content="no-cache">
    <title>Daraz Clone - Homepage</title>
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
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(90deg, var(--primary), #e55e1f);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: var(--shadow);
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

        .hero {
            background: url('https://via.placeholder.com/1200x400?text=Daraz+Banner') no-repeat center;
            background-size: cover;
            padding: 5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .hero h1, .hero p {
            position: relative;
            z-index: 2;
            animation: slideInText 1s ease-out forwards;
        }

        @keyframes slideInText {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .search-bar {
            margin: 2rem auto;
            max-width: 600px;
            text-align: center;
        }

        .search-bar form {
            display: flex;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .search-bar input {
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            flex: 1;
            transition: var(--animation-duration);
        }

        .search-bar input:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary);
            transform: scale(1.02);
        }

        .search-bar button {
            padding: 0.8rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .search-bar button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .search-bar button:hover::before {
            left: 100%;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
            opacity: 0;
            animation: fadeInCard 0.5s ease forwards;
            animation-delay: calc(var(--order) * 0.1s);
        }

        @keyframes fadeInCard {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            transition: transform var(--animation-duration), box-shadow var(--animation-duration);
        }

        .product-card .image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s;
        }

        .product-card img:hover {
            transform: scale(1.1);
        }

        .product-card .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid var(--secondary);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 2;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .product-card h3 {
            margin: 1rem;
            font-size: 1.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card .description {
            margin: 0 1rem;
            font-size: 0.9rem;
            color: #666;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card p.price {
            margin: 0.5rem 1rem;
            color: var(--primary);
            font-weight: bold;
        }

        .product-card .buttons {
            display: flex;
            gap: 0.5rem;
            margin: 1rem;
        }

        .product-card button {
            flex: 1;
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

        .product-card button.edit-btn {
            background: #4caf50;
        }

        .product-card button.delete-btn {
            background: #dc3545;
        }

        .product-card button:hover {
            background: #e55e1f;
        }

        .product-card button.edit-btn:hover {
            background: #45a049;
        }

        .product-card button.delete-btn:hover {
            background: #c82333;
        }

        .product-card button::after {
            content: '🛒';
            position: absolute;
            right: 1rem;
            opacity: 0;
            transform: translateX(20px);
            transition: var(--animation-duration);
        }

        .product-card button.edit-btn::after {
            content: '✏️';
        }

        .product-card button.delete-btn::after {
            content: '🗑️';
        }

        .product-card button:hover::after {
            opacity: 1;
            transform: translateX(0);
        }

        .product-card .quantity-input {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 1rem;
        }

        .product-card .quantity-input input {
            width: 60px;
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.8);
            text-align: center;
        }

        .product-card .quantity-input input:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary);
        }

        .error {
            color: red;
            text-align: center;
            background: #ffebee;
            padding: 0.5rem;
            border-radius: 5px;
            margin: 1rem auto;
            max-width: 600px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }

        .no-products {
            text-align: center;
            padding: 2rem;
            font-size: 1.2rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .products {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            .search-bar form {
                flex-direction: column;
            }
            .product-card .buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            .hero {
                padding: 2rem 1rem;
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="window.location.href='profile.php'">Profile</a>
                <a href="#" onclick="window.location.href='cart.php'">Cart</a>
                <?php if ($_SESSION['role'] == 'seller'): ?>
                    <a href="#" onclick="window.location.href='seller_dashboard.php'">Seller Dashboard</a>
                <?php endif; ?>
                <a href="#" onclick="window.location.href='logout.php'">Logout</a>
            <?php else: ?>
                <a href="#" onclick="window.location.href='login.php'">Login</a>
                <a href="#" onclick="window.location.href='signup.php'">Signup</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero">
        <h1>Welcome to Daraz Clone</h1>
        <p>Discover the best deals and products!</p>
    </div>
    <div class="search-bar">
        <form action="product_list.php" method="GET">
            <input type="text" name="search" placeholder="Search products...">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="products">
        <?php if (empty($products)): ?>
            <div class="no-products">No products available. <a href="#" onclick="window.location.href='product_list.php'">Browse now!</a></div>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card" style="--order: <?php echo $index + 1; ?>;">
                    <div class="image-container">
                        <div class="spinner"></div>
                        <?php
                        $image_path = $product['image'] ? $product['image'] : 'https://via.placeholder.com/150?text=No+Image';
                        if ($product['image'] && strpos($product['image'], 'http') !== 0) {
                            $image_path = $base_url . $product['image'];
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             onload="this.previousElementSibling.style.display='none';" 
                             onerror="this.src='https://via.placeholder.com/150?text=Image+Not+Found'; this.previousElementSibling.style.display='none';">
                    </div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="description"><?php echo htmlspecialchars($product['description']); ?></div>
                    <p class="price">PKR <?php echo number_format($product['price'], 2); ?></p>
                    <div class="buttons">
                        <button onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">View Details</button>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'buyer'): ?>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-input">
                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                                    <button type="submit">Add to Cart</button>
                                </div>
                            </form>
                        <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'seller' && $product['seller_id'] == $_SESSION['user_id']): ?>
                            <button class="edit-btn" onclick="window.location.href='edit_product.php?id=<?php echo $product['id']; ?>'">Edit</button>
                            <button class="delete-btn" onclick="if(confirm('Are you sure you want to delete this product?')) window.location.href='delete_product.php?id=<?php echo $product['id']; ?>'">Delete</button>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <p style="text-align: center; margin: 0.5rem;">
                            <a href="#" onclick="window.location.href='login.php'">Login to add to cart</a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
