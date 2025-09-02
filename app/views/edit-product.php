<!-- <?php
        require_once __DIR__ . '/../../app/controllers/ProductController.php';
        require_once __DIR__ . '/../../config/Db_Connect.php';
        require_once __DIR__ . '/../middleware/auth.php';

        session_start();

        // Check if logged in
        if (!isset($_SESSION['role'])) {
            header("Location: ../../public/login.php");
            exit;
        }

        // Allow only admin
        if ($_SESSION['role'] !== 'admin') {
            header("Location: ../../public/index.php");
            exit;
        }
        $controller = new ProductController();
        $errors = [];

        // Get product by ID
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Invalid product ID");
        }

        $stmt = $pdo->prepare("SELECT * FROM new_products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            die("Product not found");
        }


        // Handle update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $controller->edit($id, $_POST, $_FILES);
            if (empty($errors)) {
                header("Location: ../../public/index.php?success=2");
                exit;
            }
        }

        // Fetch categories
        $catStmt = $pdo->query("SELECT id, name, type FROM categories");
        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriesByType = [
            'physical' => [],
            'digital' => []
        ];
        foreach ($categories as $cat) {
            $categoriesByType[strtolower($cat['type'])][] = $cat;
        }
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
</head>
<body>
    <h1>Edit Product</h1>
    <p><a href="../../public/index.php">← Back to Products</a></p>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($product['email']) ?>" required><br><br>

        <label>Category Type:</label>
        <select id="category_type" name="category_type" required onchange="updateCategoryDropdown()">
            <option value="">-- Select Type --</option>
            <option value="physical" <?= strtolower($product['weight']) !== "" ? "selected" : "" ?>>Physical</option>
            <option value="digital" <?= $product['file_link'] ? "selected" : "" ?>>Digital</option>
        </select><br><br>

        <label>Category:</label>
        <select id="category" name="category" required>
            <option value="">-- Select Category --</option>
        </select><br><br>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required><br><br>

        <div id="weightField" style="display:none;">
            <label>Weight:</label>
            <input type="text" name="weight" value="<?= htmlspecialchars($product['weight']) ?>"><br><br>
        </div>

        <div id="fileField" style="display:none;">
            <label>File:</label>
            <input type="file" name="file_link"><br>
            <?php if ($product['file_link']): ?>
                <small>Current: <?= htmlspecialchars($product['file_link']) ?></small>
            <?php endif; ?>
            <br><br>
        </div>

        <button type="submit">Update Product</button>
    </form>

    <script>
        const categoriesByType = <?php echo json_encode($categoriesByType); ?>;
        const currentCategory = <?= (int)$product['category_id'] ?>;
        const currentType = "<?= $product['weight'] !== null ? 'physical' : 'digital' ?>";

        function updateCategoryDropdown() {
            const typeSelect = document.getElementById('category_type');
            const categorySelect = document.getElementById('category');
            const type = typeSelect.value.toLowerCase();

            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            if (categoriesByType[type]) {
                categoriesByType[type].forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    if (cat.id === currentCategory) opt.selected = true;
                    categorySelect.appendChild(opt);
                });
            }
        }

        document.getElementById('category_type').addEventListener('change', function () {
            document.getElementById('weightField').style.display = (this.value === 'physical') ? 'block' : 'none';
            document.getElementById('fileField').style.display = (this.value === 'digital') ? 'block' : 'none';
        });

        // Init on load
        document.getElementById('category_type').value = currentType;
        updateCategoryDropdown();
        if (currentType === 'physical') {
            document.getElementById('weightField').style.display = 'block';
        } else {
            document.getElementById('fileField').style.display = 'block';
        }
    </script>
</body>
</html> -->













<?php
require_once __DIR__ . '/../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../config/Db_Connect.php';
require_once __DIR__ . '/../middleware/auth.php';

$controller = new ProductController();
$errors = [];

// Get product by ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid product ID");
}

$stmt = $pdo->prepare("SELECT * FROM new_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("Product not found");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->edit($id, $_POST, $_FILES);
    if (empty($errors)) {
        header("Location: ../../public/index.php?success=2");
        exit;
    }
}

// Fetch categories
$catStmt = $pdo->query("SELECT id, name, type FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$categoriesByType = [
    'physical' => [],
    'digital' => []
];
foreach ($categories as $cat) {
    $categoriesByType[strtolower($cat['type'])][] = $cat;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* Minimal reset */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* Better sizing model [8] */
        html,
        body {
            margin: 0;
            padding: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Base legibility [8] */
        img {
            max-width: 100%;
            display: block;
        }

        /* Safe default [8] */
        input,
        button,
        select,
        textarea {
            font: inherit;
        }

        /* Consistent form fonts [8] */

        /* Page layout */
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            /* Neutral stack [6] */
            background: #f7f7f8;
            /* Soft gray background [1] */
            color: #1f2328;
            /* High-contrast neutral text [7] */
        }

        .container {
            max-width: 720px;
            margin: 32px auto;
            padding: 0 16px;
        }

        /* Header + link */
        h1 {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 16px;
            color: #0b57d0;
            text-decoration: none;
        }

        .back-link:hover,
        .back-link:focus {
            text-decoration: underline;
        }

        /* Card */
        .card {
            background: #fff;
            border: 1px solid #e6e8eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        }

        /* Errors */
        .errors {
            border: 1px solid #f1c0c0;
            background: #fff5f5;
            color: #8a1f1f;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        /* Form */
        form {
            display: grid;
            gap: 14px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d0d7de;
            border-radius: 8px;
            background: #fff;
            color: inherit;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input[type="file"] {
            padding: 8px 10px;
            /* native control */
        }

        input:focus,
        select:focus {
            border-color: #0b57d0;
            box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.15);
        }

        /* Grouped fields spacing */
        .field {
            margin-bottom: 6px;
        }

        .hint {
            font-size: 0.9rem;
            color: #57606a;
        }

        /* Conditional sections */
        #weightField,
        #fileField {
            padding: 12px;
            border: 1px dashed #e6e8eb;
            border-radius: 8px;
            background: #fafbfc;
        }

        /* Button */
        button[type="submit"] {
            appearance: none;
            border: 1px solid #127709ff;
            background: #2c8d06ff;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, box-shadow 0.15s ease;
        }

        button[type="submit"]:hover,
        button[type="submit"]:focus {
            background: #0f4402ff;
            box-shadow: 0 2px 8px rgba(52, 223, 89, 0.25);
        }

        /* Small screens */
        @media (max-width: 480px) {
            .card {
                padding: 16px;
            }

            h1 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Product</h1>
        <p><a class="back-link" href="../../public/index.php">← Back to Products</a></p>

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

            <form method="POST" enctype="multipart/form-data">
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
                    <select id="category_type" name="category_type" required onchange="updateCategoryDropdown()">
                        <option value="">-- Select Type --</option>
                        <option value="physical" <?= strtolower($product['weight']) !== "" ? "selected" : "" ?>>Physical</option>
                        <option value="digital" <?= $product['file_link'] ? "selected" : "" ?>>Digital</option>
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

                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" 
                    value="<?php echo htmlspecialchars($product['stock']); ?>"
                    required>


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

    <script>
        const categoriesByType = <?php echo json_encode($categoriesByType); ?>;
        const currentCategory = <?= (int)$product['category_id'] ?>;
        const currentType = "<?= $product['weight'] !== null ? 'physical' : 'digital' ?>";

        function updateCategoryDropdown() {
            const typeSelect = document.getElementById('category_type');
            const categorySelect = document.getElementById('category');
            const type = typeSelect.value.toLowerCase();

            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            if (categoriesByType[type]) {
                categoriesByType[type].forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    if (cat.id === currentCategory) opt.selected = true;
                    categorySelect.appendChild(opt);
                });
            }
        }

        document.getElementById('category_type').addEventListener('change', function() {
            document.getElementById('weightField').style.display = (this.value === 'physical') ? 'block' : 'none';
            document.getElementById('fileField').style.display = (this.value === 'digital') ? 'block' : 'none';
        });

        // Init on load
        document.getElementById('category_type').value = currentType;
        updateCategoryDropdown();
        if (currentType === 'physical') {
            document.getElementById('weightField').style.display = 'block';
        } else {
            document.getElementById('fileField').style.display = 'block';
        }
    </script>
</body>

</html>