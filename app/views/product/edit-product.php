<?php
require_once __DIR__ . '/../../../config/Db_Connect.php';
require_once __DIR__ . '/../../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../middleware/auth.php';
session_start();

// Check if data is available from the logic file
if (!isset($_SESSION['product_data'])) {
    header("Location: /../../../public/index.php"); // Redirect if accessed directly
    exit;
}

$product = $_SESSION['product_data'];
$categoriesByType = $_SESSION['categories'];
$errors = $_SESSION['edit_errors'] ?? [];

// Clear session variables after use
unset($_SESSION['product_data'], $_SESSION['categories'], $_SESSION['edit_errors']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/../../../public/css/edit_product.css">
</head>

<body>
    <div class="container">
        <h1>Edit Product</h1>
        <p><a class="back-link" href="/../../../public/index.php">← Back to Products</a></p>

        <div class="card">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/../../app/helper/process_edit_product.php?id=<?= htmlspecialchars($product['id']) ?>" method="POST" enctype="multipart/form-data">
                <div class="field">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="field">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($product['email']) ?>" required>
                </div>

                <div class="field">
                    <label>Category Type:</label>
                    <select id="category_type" name="category_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="physical">Physical</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>

                <div class="field">
                    <label>Category:</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                    </select>
                </div>

                <div class="field">
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>

                <div class="field">
                    <label for="stock">Stock</label>
                    <input type="number" name="stock" id="stock" min="0" value="<?= htmlspecialchars($product['stock']); ?>" required>
                </div>

                <div id="weightField" style="display:none;">
                    <div class="field">
                        <label>Weight:</label>
                        <input type="text" name="weight" value="<?= htmlspecialchars($product['weight']) ?>">
                    </div>
                </div>

                <div id="fileField" style="display:none;">
                    <div class="field">
                        <label>File:</label>
                        <input type="file" name="file_link">
                        <?php if ($product['file_link']): ?>
                            <div class="hint">Current: <?= htmlspecialchars($product['file_link']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit">Update Product</button>
            </form>
        </div>
    </div>

    <!-- Pass the initial values and categories to the JS file -->
    <script>
        const categoriesByType = <?php echo json_encode($categoriesByType); ?>;
        const currentCategory = <?= (int)$product['category_id'] ?>;
        const currentType = "<?= $product['weight'] !== null ? 'physical' : 'digital' ?>";
    </script>
    <script src="/../../../public/js/editproduct.js"></script>
</body>

</html>
