<?php
require "Db_Connect.php";

// Fetch products with category
$stmt = $pdo->query("
    SELECT p.id, p.email, p.name, p.price, p.created_at, c.name AS category 
    FROM new_products p 
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        a {
            text-decoration: none;
            color: white;
            background: #28a745;
            padding: 8px 15px;
            border-radius: 5px;
        }

        a:hover {
            background: #218838;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn-edit {
            background: #ffc107;
            color: black;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <h2>📦 Product List</h2>
    <div style="text-align:center; margin-bottom:15px;">
        <a href="Add.php">➕ Add Product</a>
    </div>
    <table>
        <tr>
            <th>Sl No</th>
            <th>Email</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
        <?php
        $sl = 1;
        foreach ($products as $p): ?>
            <tr>
                <td><?= $sl++ ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td>$<?= htmlspecialchars($p['price']) ?></td>
                <td><?= $p['category'] ?? 'Uncategorized' ?></td>
                <td><?= date("d-M-Y h:i A", strtotime($p['created_at'])) ?></td>
                <td>
                    <a href="edit.php?id=<?= $p['id'] ?>" class="btn-edit">✏️ Edit</a>
                    <a href="delete.php?id=<?= $p['id'] ?>" class="btn-delete"
                        onclick="return confirm('Are you sure you want to delete this product?');">
                        🗑 Delete
                    </a>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>