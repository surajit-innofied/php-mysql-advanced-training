<link rel="stylesheet" href="/../../../public/css/product_list.css">
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
            <?php if ($userController->isLoggedIn() && $_SESSION['user']['role'] === 'user'): ?>

                <th>Cart</th>
            <?php endif; ?>

            <?php if ($userController->isLoggedIn() && $_SESSION['user']['role'] === 'admin'): ?>
                <th>Stock</th>
                <th>Deleted</th>
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

                        <!-- show stock -->
                        <td><?= htmlspecialchars($p['stock']) ?></td>

                        <!-- show deleted status -->
                        <td>
                            <?= ($p['is_deleted'] == 1) ? "<span style='color:red;font-weight:bold;'>Yes</span>" : "No"; ?>
                        </td>

                        <td>
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <?php if ($p['is_deleted'] == 0): ?>
                                    <!-- Edit Button -->
                                    <form method="GET" action="../app/helper/process_edit_product.php">
                                        <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                        <button type="submit" class="btn small">Edit</button>
                                    </form>

                                    <!-- Delete Button -->
                                    <form action="../../app/helper/process_delete_product.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn small danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#666; font-size:13px;">(No Action)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    <?php endif; ?>

                    <!-- Add to cart for user  -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                        <td>
                            <?php if ((int)$p['stock'] > 0): ?>
                                <form method="POST" action="../app/controllers/add-cart.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= (int)$p['id']; ?>">
                                    <button type="submit" class="btn small">Add to Cart</button>
                                </form>
                                <!-- when stock is 0 -->
                            <?php else: ?>
                                <span style="display:inline-block; padding:4px 8px; border-radius:4px; background:#eee; color:#999; font-size:13px;">
                                    Out of Stock
                                </span>
                            <?php endif; ?>
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