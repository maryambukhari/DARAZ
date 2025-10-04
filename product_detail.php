<?php
// product_detail.php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// Check if image exists
$image_path = $product['image'] && file_exists($product['image']) ? $product['image'] : 'https://via.placeholder.com/300?text=No+Image';

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($quantity <= 0 || $quantity > $product['stock']) {
        $error = "Invalid quantity or insufficient stock!";
    } else {
        // Check if product is already in cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            // Update quantity
            $new_quantity = $cart_item['quantity'] + $quantity;
            if ($new_quantity > $product['stock']) {
                $error = "Cannot add more than available stock!";
            } else {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$new_quantity, $user_id, $product_id]);
                echo "<script>window.location.href='cart.php';</script>";
                exit;
            }
        } else {
            // Add new item to cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
            echo "<script>window.location.href='cart.php';</script>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Daraz Clone</title>
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

        /* Product Detail Container */
        .product-detail {
            display: flex;
            max-width: 1200px;
            margin: 100px auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .product-image {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .product-image img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 10px;
            transition: transform 0.5s, box-shadow 0.3s, opacity 0.5s;
            animation: fadeInImage 0.7s ease-out;
            object-fit: cover;
        }

        .product-image img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeInImage {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* Image Loading Spinner */
        .product-image .spinner {
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
            display: none; /* Hidden by default, shown via JS if needed */
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .product-info {
            flex: 1;
            padding: 1rem;
        }

        .product-info h2 {
            color: var(--primary);
            margin-bottom: 1rem;
            animation: fadeInText 0.7s ease-out;
        }

        .product-info p {
            margin: 0.5rem 0;
            line-height: 1.6;
        }

        .product-info .price {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }

        .product-info .stock {
            color: <?php echo $product['stock'] > 0 ? 'green' : 'red'; ?>;
            font-weight: 500;
        }

        /* Quantity Input */
        .quantity-input {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
        }

        .quantity-input input {
            width: 60px;
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.8);
            text-align: center;
            transition: var(--animation-duration);
        }

        .quantity-input input:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary);
        }

        /* Add to Cart Button */
        .add-to-cart {
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

        .add-to-cart:hover {
            background: #e55e1f;
        }

        .add-to-cart::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .add-to-cart:hover::before {
            left: 100%;
        }

        .add-to-cart::after {
            content: '🛒';
            position: absolute;
            right: 1rem;
            opacity: 0;
            transform: translateX(20px);
            transition: var(--animation-duration);
        }

        .add-to-cart:hover::after {
            opacity: 1;
            transform: translateX(0);
        }

        /* Error Message */
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

        @keyframes fadeInText {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-detail {
                flex-direction: column;
                padding: 1rem;
            }
            .product-image img {
                max-height: 300px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            .product-info h2 {
                font-size: 1.5rem;
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
    <div class="product-detail">
        <div class="product-image">
            <div class="spinner"></div>
            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onload="this.previousElementSibling.style.display='none'" onerror="this.src='https://via.placeholder.com/300?text=Image+Not+Found';">
        </div>
        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
            <p class="price">PKR <?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            <p class="stock">Stock: <?php echo $product['stock'] > 0 ? $product['stock'] . ' available' : 'Out of stock'; ?></p>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($product['stock'] > 0 && isset($_SESSION['user_id'])): ?>
                <form method="POST">
                    <div class="quantity-input">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                    </div>
                    <button type="submit" class="add-to-cart">Add to Cart</button>
                </form>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <p>Please <a href="#" onclick="window.location.href='login.php'">login</a> to add to cart.</p>
            <?php else: ?>
                <p>Out of stock!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
