<?php
// Include database connection
require "Db_Connect.php";

// Fetch all products with category info (including weight/file_link for specific types)
$stmt = $pdo->query("
    SELECT p.id, p.email, p.name, p.price, 
           p.weight, p.file_link,
           c.name AS category_name, c.type AS category_type
    FROM new_products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results as associative array
?>

<html>
<head>
    <title>Products</title>
    <style>
        /* Basic page styling */
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f2f2f2; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }

        /* Action button styles */
        .btn-delete { color: white; background: #dc3545; padding: 5px 10px; border-radius: 5px; }
        .btn-edit { color: white; background: #007bff; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Products List</h2>

    <!-- Link to Add New Product -->
    <a href="Add.php">➕ Add Product</a>
    <br><br>

    <!-- Products Table -->
    <table>
        <tr>
            <th>S.No</th>
            <th>Email</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Type</th>
            <th>Weight / File Link</th>
            <th>Actions</th>
        </tr>

        <!-- Loop through all products and display them -->
        <?php $sn = 1; foreach ($products as $p): ?>
            <tr>
                <!-- Serial Number -->
                <td><?= $sn++ ?></td>

                <!-- Email -->
                <td><?= htmlspecialchars($p['email']) ?></td>

                <!-- Product Name -->
                <td><?= htmlspecialchars($p['name']) ?></td>

                <!-- Product Price -->
                <td><?= htmlspecialchars($p['price']) ?></td>

                <!-- Category Name -->
                <td><?= htmlspecialchars($p['category_name']) ?></td>

                <!-- Category Type (Physical / Digital) -->
                <td><?= htmlspecialchars($p['category_type']) ?></td>

                <!-- Show Weight (for Physical) OR File Download Link (for Digital) -->
                <td>
                    <?php if (strtolower($p['category_type']) === 'physical'): ?>
                        <?= htmlspecialchars($p['weight'] ?? '-') ?>
                    <?php elseif (strtolower($p['category_type']) === 'digital'): ?>
                        <?php if (!empty($p['file_link'])): ?>
                            <a href="<?= htmlspecialchars($p['file_link']) ?>" target="_blank">Download</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>

                <!-- Edit & Delete Buttons -->
                <td>
                    <!-- Edit Product -->
                    <a class="btn-edit" href="edit.php?id=<?= $p['id'] ?>">✏ Edit</a>

                    <!-- Delete Product (with confirmation) -->
                    <a class="btn-delete" href="delete.php?id=<?= $p['id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this product?')">🗑 Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
