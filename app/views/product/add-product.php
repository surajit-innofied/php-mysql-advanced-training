<?php
require_once __DIR__ . '/../../../config/Db_Connect.php';
require_once __DIR__ . '/../../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../middleware/auth.php';
$controller = new ProductController();
$categoriesByType = $controller->getCategories();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="/../../../public/css/add_product.css">
</head>

<body>
    <h1>Add New Product</h1>
    <div class="container">
        <a href="/../../../public/index.php" class="back-btn">← Back to Products</a>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/../../app/helper/process_add_product.php" method="POST" enctype="multipart/form-data">
            <label for="name"> Name:</label>
            <input type="text" name="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="category_type">Category Type:</label>
            <select id="category_type" name="category_type" onchange="updateCategoryDropdown()" required>
                <option value="">-- Select Type --</option>
                <option value="physical">Physical</option>
                <option value="digital">Digital</option>
            </select>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
            </select>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" required>
            
            <label for="stock">Stock Count:</label>
            <input type="number" name="stock" id="stock" min="0" value="0" required>


            <div id="weightField" style="display:none;">
                <label for="weight">Weight:</label>
                <input type="text" name="weight">
            </div>

            <div id="fileField" style="display:none;">
                <label for="file_link">File:</label>
                <input type="file" name="file_link">
            </div>

            <button type="submit">Add Product</button>
        </form>
    </div>

   <!-- Pass the categories data to the JavaScript file -->
    <script>
        const categoriesByType = <?php echo json_encode($categoriesByType); ?>;
    </script>
    <script src="/../../../public/js/addproduct.js"></script>
</body>

</html>