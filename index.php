<?php
require "Db_Connect.php";

// Fetch all products with category + type + weight/file_link
$stmt = $pdo->query("
    SELECT p.id, p.email, p.name, p.price, 
           p.weight, p.file_link,
           c.name AS category_name, c.type AS category_type
    FROM new_products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <title>Products</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f2f2f2; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .btn-delete { color: white; background: #dc3545; padding: 5px 10px; border-radius: 5px; }
        .btn-edit { color: white; background: #007bff; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Products List</h2>
    <a href="Add.php">➕ Add Product</a>
    <br><br>
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
        <?php $sn = 1; foreach ($products as $p): ?>
            <tr>
                <td><?= $sn++ ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['price']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= htmlspecialchars($p['category_type']) ?></td>
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
                <td>
                    <a class="btn-edit" href="edit.php?id=<?= $p['id'] ?>">✏ Edit</a>
                    <a class="btn-delete" href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
