<style>
    .container {
        width: auto;
        /* instead of fixed px/percent */
        min-width: 90%;
        /* keeps layout consistent */
        max-width: 100%;
        /* full stretch with page */
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Container */
    .product-container {
        width: 100%;
        margin: 20px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    /* Table Styling */
    .product-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 15px;
    }

    .product-table th,
    .product-table td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    .product-table th {
        background: #007bff;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-table tr:nth-child(even) {
        background: #f9f9f9;
    }

    .product-table tr:hover {
        background: #f1f7ff;
        transition: 0.2s;
    }

    /* Buttons */
    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        display: inline-block;
    }

    .btn.small {
        padding: 4px 10px;
        font-size: 13px;
    }

    .btn.download {
        background: #28a745;
        color: white;
    }

    .btn.download:hover {
        background: #218838;
    }

    .btn.danger {
        background: #dc3545;
        color: white;
    }

    .btn.danger:hover {
        background: #c82333;
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>
<div class="product-container">
    <table class="product-table">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Category Type</th>
            <th>Category</th>
            <th>Price</th>
            <th>Weight</th>
            <th>File</th>
            <?php if ($userController->isLoggedIn() && $_SESSION['user']['role'] === 'admin'): ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>

        <?php if (!empty($products)): ?>
            <?php $count = 1 ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['category_type']) ?></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td><?= htmlspecialchars($p['price']) ?></td>
                    <td><?= htmlspecialchars($p['weight'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($p['file_link'])): ?>
                            <a href="uploads/<?= htmlspecialchars($p['file_link']) ?>" target="_blank" class="btn small download">Download</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <?php if ($userController->isLoggedIn() && $_SESSION['user']['role'] === 'admin'): ?>
                        <td>
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <!-- Edit Button -->
                                <form method="GET" action="../app/views/edit-product.php">
                                    <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                    <button type="submit" class="btn small">Edit</button>
                                </form>

                                <!-- Delete Button -->
                                <form method="POST" action="index.php"
                                    onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="delete_id" value="<?= $p['id']; ?>">
                                    <button type="submit" class="btn small danger">Delete</button>
                                </form>
                            </div>
                        </td>

                    <?php endif; ?>

                    <?php $count++ ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= ($userController->isLoggedIn() && $_SESSION['user']['role'] === 'admin') ? 9 : 8 ?>">
                    No products found.
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>