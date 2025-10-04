<?php
// edit_product.php
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

$product_id = $_GET['id'];
$seller_id = $_SESSION['user_id'];

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->execute([$product_id, $seller_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<script>window.location.href='seller_dashboard.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $image = $product['image']; // Keep existing image if no new upload

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image = 'uploads/' . $image_name;
        $target = $image;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $error = "Only JPEG, PNG, or GIF images are allowed!";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $error = "Image size must be less than 5MB!";
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $error = "Failed to upload image!";
        }
    }

    if (!isset($error)) {
        // Update product
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ?, image = ? WHERE id = ? AND seller_id = ?");
        try {
            $stmt->execute([$name, $description, $price, $category, $stock, $image, $product_id, $seller_id]);
            echo "<script>window.location.href='seller_dashboard.php';</script>";
            exit;
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Daraz Clone</title>
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

        /* Form Container */
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

        .form-container label {
            display: block;
            margin: 0.5rem 0;
            font-weight: 500;
        }

        .form-container input,
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.8);
            transition: var(--animation-duration);
        }

        .form-container input:focus,
        .form-container textarea:focus,
        .form-container select:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary);
            transform: scale(1.02);
        }

        .form-container textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-container button {
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

        .form-container button:hover {
            background: #e55e1f;
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

        /* Image Preview */
        .image-preview {
            margin: 1rem 0;
            text-align: center;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 5px;
            transition: transform 0.3s;
        }

        .image-preview img:hover {
            transform: scale(1.05);
        }

        /* Responsive Design */
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
        <h2>Edit Product</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <label>Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <label>Price (PKR)</label>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required>
            <label>Category</label>
            <select name="category" required>
                <option value="Electronics" <?php if ($product['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                <option value="Fashion" <?php if ($product['category'] == 'Fashion') echo 'selected'; ?>>Fashion</option>
                <option value="Home & Kitchen" <?php if ($product['category'] == 'Home & Kitchen') echo 'selected'; ?>>Home & Kitchen</option>
            </select>
            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
            <label>Current Image</label>
            <div class="image-preview">
                <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/150?text=No+Image'); ?>" alt="Current Image">
            </div>
            <label>Upload New Image (Optional)</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>
